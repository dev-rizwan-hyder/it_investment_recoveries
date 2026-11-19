<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EbaySummaryController extends Controller
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'];
    private const IMAGE_SEARCH_DISTANCE = 10;
    private const DENVER_TIMEZONE = 'America/Denver';
    private const PRODUCTS_CACHE_KEY = 'summary.ebay.products.v2';
    private const PRODUCTS_CACHE_SECONDS = 45;
    private const TEMP_DIR = 'ebay-temp';

    public function index(Request $request): Response
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();
        $status = in_array($status, ['done', 'remaining'], true) ? $status : '';
        $folderTypeSearch = $request->string('folder_type')->toString();
        $folderTypeSearch = in_array($folderTypeSearch, ['Ebay', 'Denver', 'Both'], true) ? $folderTypeSearch : '';
        
        $allProducts = $this->products();
        $products = $allProducts;

        if ($search !== '') {
            $needle = strtolower($search);

            $products = array_values(array_filter($products, function ($product) use ($needle) {
                $haystack = strtolower(implode(' ', [
                    $product['folder_name'] ?? '',
                    $product['product_name'] ?? '',
                    $product['folder'] ?? '',
                    $product['location'] ?? '',
                    $product['folder_type'] ?? 'Ebay',
                ]));

                return str_contains($haystack, $needle);
            }));
        }

        if ($status !== '') {
            $products = array_values(array_filter($products, function ($product) use ($status) {
                $marked = $product['marked'] ?? false;
                $isMarked = $marked !== false && $marked !== 'remaining';
                return $status === 'done' ? $isMarked : !$isMarked;
            }));
        }

        if ($folderTypeSearch !== '') {
            $products = array_values(array_filter($products, function ($product) use ($folderTypeSearch) {
                $type = $product['folder_type'] ?? 'Ebay';
                if ($folderTypeSearch === 'Both') {
                    return $type === 'Ebay' || $type === 'Denver' || $type === 'Both';
                }
                if ($folderTypeSearch === 'Ebay') {
                    return $type === 'Ebay' || $type === 'Both';
                }
                if ($folderTypeSearch === 'Denver') {
                    return $type === 'Denver' || $type === 'Both';
                }
                return $type === $folderTypeSearch;
            }));
        }

        return $this->noIndex('ebay.summary', [
            'products' => $products,
            'stats' => $this->statsForProducts($products),
            'search' => $search,
            'status' => $status,
            'folderTypeSearch' => $folderTypeSearch,
            'imageSearch' => false,
            'imageSearchName' => '',
            'hasIncompleteUploads' => $this->hasIncompleteUploads($allProducts),
        ]);
    }

    public function imageSearch(Request $request): Response
    {
        $request->validate([
            'search_image' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp',
                'max:20480',
            ],
        ]);

        $searchImage = $request->file('search_image');
        $searchHash = $this->perceptualHash($searchImage->getRealPath());

        if (!$searchHash) {
            return back()
                ->withErrors(['search_image' => 'This image type could not be compared. Please use JPG, PNG, GIF, or WEBP.']);
        }

        $allProducts = $this->products();
        $products = collect($allProducts)
            ->map(function ($product) use ($searchHash) {
                $match = $this->bestImageMatch($product, $searchHash);

                if (!$match) {
                    return null;
                }

                $product['similarity_match'] = $match;

                return $product;
            })
            ->filter(fn ($product) => $product['similarity_match']['distance'] <= self::IMAGE_SEARCH_DISTANCE)
            ->sortBy(fn ($product) => $product['similarity_match']['distance'])
            ->values()
            ->all();

        return $this->noIndex('ebay.summary', [
            'products' => $products,
            'stats' => $this->statsForProducts($products),
            'search' => '',
            'status' => '',
            'folderTypeSearch' => '',
            'imageSearch' => true,
            'imageSearchName' => $searchImage->getClientOriginalName(),
            'hasIncompleteUploads' => $this->hasIncompleteUploads($allProducts),
        ]);
    }

    /**
     * Remove image background using the shared remove.bg service.
     */
    public function removeBackground(Request $request)
    {
        $request->validate([
            'image' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:10240',
            ],
        ]);

        $apiKey = config('services.remove_bg.api_key');
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Background removal API key is not configured. Add REMOVE_BG_API_KEY to your .env file.',
            ], 422);
        }

        $image = $request->file('image');

        try {
            $response = Http::timeout(60)
                ->withHeaders(['X-Api-Key' => $apiKey])
                ->attach(
                    'image_file',
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName() ?: 'product-photo.jpg'
                )
                ->post('https://api.remove.bg/v1.0/removebg', [
                    'size' => config('services.remove_bg.size', 'preview'),
                    'format' => 'png',
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json('errors.0.title')
                        ?? $response->json('errors.0.detail')
                        ?? 'Background removal failed.',
                ], $response->status() >= 400 ? $response->status() : 502);
            }

            return response($response->body(), 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store',
            ]);
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Background removal service could not be reached.',
            ], 502);
        }
    }

    /**
     * Upload images immediately (pre-upload) to a temp directory.
     * Returns temp IDs that can be used to create a product folder quickly.
     */
    public function uploadTempImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif',
                'max:20480',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid image.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $files = $this->uploadedImages($request);
        $uploaded = [];

        foreach ($files as $file) {
            $tempId       = Str::uuid()->toString();
            $extension    = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
            $extension    = preg_replace('/[^a-z0-9]+/', '', $extension) ?: 'jpg';
            $originalBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename     = (Str::slug($originalBase ?: 'image') ?: 'image') . '.' . $extension;
            $tempDir      = self::TEMP_DIR . '/' . $tempId;

            Storage::disk('local')->makeDirectory($tempDir);
            $stored = $file->storeAs($tempDir, $filename, 'local');

            if (!$stored) {
                return response()->json([
                    'message' => 'Failed to store image. Check server storage permissions.',
                ], 500);
            }

            $uploaded[] = [
                'temp_id'       => $tempId,
                'filename'      => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ];
        }

        return response()->json(['uploaded' => $uploaded]);
    }

    /**
     * Create a new product folder and store the uploaded images directly.
     */
    public function store(Request $request)
    {
        $data  = $request->validate(array_merge($this->metadataRules(), $this->imageRules()));
        $files = $this->uploadedImages($request);

        if (count($files) === 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please add at least one image.',
                    'errors'  => ['images' => ['Please add at least one image.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['images' => 'Please add at least one image.']);
        }

        $product = $this->createProductFolder($data, $files);

        if ($request->expectsJson()) {
            $allProducts = $this->products();
            return response()->json([
                'message' => 'Product folder created.',
                'product' => $this->productPayload($product),
                'stats'   => $this->statsForProducts($allProducts),
                'html'    => view('ebay.partials.product-card', ['product' => $product])->render(),
            ], 201);
        }

        return redirect()->route('summary.ebay.index')->with('success', 'Product folder created.');
    }

    public function uploadImages(Request $request, string $folder)
    {
        $validator = Validator::make($request->all(), $this->imageRules());

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please add at least one valid image.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $redirect = redirect()
                ->route('summary.ebay.index')
                ->withErrors($validator, $request->boolean('return_to_edit') ? 'editProduct' : 'default');

            return $request->boolean('return_to_edit')
                ? $redirect->with('edit_folder', $folder)
                : $redirect;
        }

        $product = $this->product($folder);

        if (!$product) {
            abort(404);
        }

        $files = $this->uploadedImages($request);

        if (count($files) === 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please add at least one image.',
                    'errors' => [
                        'images' => ['Please add at least one image.'],
                    ],
                ], 422);
            }

            $redirect = redirect()
                ->route('summary.ebay.index')
                ->withErrors(['images' => 'Please add at least one image.'], $request->boolean('return_to_edit') ? 'editProduct' : 'default');

            return $request->boolean('return_to_edit')
                ? $redirect->with('edit_folder', $folder)
                : $redirect;
        }

        $metadata = $product['metadata'];
        $existingImages = array_values($metadata['images'] ?? []);
        $metadata['images'] = array_merge(
            $existingImages,
            $this->storeUploadedImages($files, $product['path'], count($existingImages))
        );
        $metadata['upload_status'] = 'complete';
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($product['path'], $metadata);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Images uploaded.',
                'product' => $this->productPayload($this->product($folder) ?? $product),
            ]);
        }

        $redirect = redirect()
            ->route('summary.ebay.index')
            ->with('success', 'Images uploaded.');

        return $request->boolean('return_to_edit')
            ? $redirect->with('edit_folder', $folder)
            : $redirect;
    }

    public function destroyImage(string $folder, string $filename)
    {
        $product = $this->product($folder);

        if (!$product) {
            abort(404);
        }

        $requestedFilename = $filename;
        $filename = basename($filename);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($filename === '' || $filename !== $requestedFilename || !in_array($extension, self::IMAGE_EXTENSIONS, true)) {
            abort(404);
        }

        $imagePath = $product['path'] . DIRECTORY_SEPARATOR . $filename;
        $fileExists = File::isFile($imagePath);
        $metadata = $product['metadata'];
        $imageFound = false;
        $metadata['images'] = collect($metadata['images'] ?? [])
            ->filter(function ($image) use ($filename, &$imageFound) {
                if (!is_array($image) || !isset($image['filename'])) {
                    return false;
                }

                if (basename($image['filename']) === $filename) {
                    $imageFound = true;

                    return false;
                }

                return true;
            })
            ->values()
            ->all();

        if (!$fileExists && !$imageFound) {
            abort(404);
        }

        if ($fileExists) {
            File::delete($imagePath);
        }

        $metadata['upload_status'] = count($metadata['images']) > 0 ? 'complete' : 'pending';
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($product['path'], $metadata);

        return redirect()
            ->route('summary.ebay.index')
            ->with('success', 'Image deleted.')
            ->with('edit_folder', $folder);
    }

    public function update(Request $request, string $folder)
    {
        $product = $this->product($folder);

        if (!$product) {
            abort(404);
        }

        $validator = Validator::make($request->all(), array_merge($this->metadataRules(), [
            'marked' => ['nullable', 'string', 'in:remaining,ebay,denver,both,abid,moses,0,1'],
        ]));

        if ($validator->fails()) {
            return redirect()
                ->route('summary.ebay.index')
                ->withErrors($validator, 'editProduct')
                ->withInput()
                ->with('edit_folder', $folder);
        }

        $data = $validator->validated();
        $metadata = $product['metadata'];
        $metadata['folder_name'] = trim($data['folder_name']);
        $metadata['condition'] = $data['condition'];
        $metadata['folder_type'] = $data['folder_type'] ?? 'Ebay';
        $metadata['location'] = trim($data['location'] ?? '');
        $metadata['quantity'] = (int) $data['quantity'];
        $metadata['notes'] = trim($data['notes'] ?? '');
        $metadata['ebay_link'] = trim($data['ebay_link'] ?? '');
        $metadata['website_link'] = trim($data['website_link'] ?? '');
        $metadata['social_media_1'] = trim($data['social_media_1'] ?? '');
        $metadata['social_media_2'] = trim($data['social_media_2'] ?? '');
        $metadata['social_media_3'] = trim($data['social_media_3'] ?? '');
        $markedInput = $request->input('marked', 'remaining');
        if (in_array($markedInput, ['1', 'true', true], true)) {
            $markedInput = 'both';
        } elseif (in_array($markedInput, ['0', 'false', false, null, '', 'remaining'], true)) {
            $markedInput = 'remaining';
        }
        $metadata['marked'] = $markedInput;
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($product['path'], $metadata);

        return redirect()
            ->route('summary.ebay.index')
            ->with('success', 'Product details updated.');
    }

    public function toggleMark(string $folder)
    {
        $product = $this->product($folder);

        if (!$product) {
            abort(404);
        }

        $metadata = $product['metadata'];
        $current = $metadata['marked'] ?? false;
        // Cycle: remaining/false ➔ abid ➔ moses ➔ both ➔ remaining
        if ($current === false || $current === 'remaining') {
            $newMarked = 'abid';
        } elseif ($current === 'ebay' || $current === 'abid' || $current === 1) {
            $newMarked = 'moses';
        } elseif ($current === 'denver' || $current === 'moses' || $current === 2) {
            $newMarked = 'both';
        } else {
            $newMarked = 'remaining';
        }
        $metadata['marked'] = $newMarked;
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($product['path'], $metadata);

        return redirect()
            ->route('summary.ebay.index')
            ->with('success', 'Folder status updated.');
    }

    public function destroy(string $folder)
    {
        $product = $this->product($folder);

        if (!$product) {
            abort(404);
        }

        File::deleteDirectory($product['path']);
        $this->forgetProductsCache();

        return redirect()
            ->route('summary.ebay.index')
            ->with('success', 'Product folder deleted.');
    }

    private function metadataRules(): array
    {
        return [
            'folder_name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', 'in:New,Used,Refurbished'],
            'folder_type' => ['required', 'string', 'in:Ebay,Denver,Both'],
            'location' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'ebay_link' => ['nullable', 'string', 'max:2048'],
            'website_link' => ['nullable', 'string', 'max:2048'],
            'social_media_1' => ['nullable', 'string', 'max:2048'],
            'social_media_2' => ['nullable', 'string', 'max:2048'],
            'social_media_3' => ['nullable', 'string', 'max:2048'],
        ];
    }

    private function imageRules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif',
                'max:20480',
            ],
        ];
    }

    private function createProductFolder(array $data, array $files = []): array
    {
        $basePath = $this->basePath();
        File::ensureDirectoryExists($basePath, 0755);

        $folderName = trim($data['folder_name']);
        [$folder, $folderPath] = $this->createUniqueProductDirectory($basePath, $folderName);

        $createdAt = $this->denverNow()->toIso8601String();
        $metadata = [
            'folder'        => $folder,
            'folder_name'   => $folderName,
            'condition'     => $data['condition'],
            'folder_type'   => $data['folder_type'] ?? 'Ebay',
            'location'      => trim($data['location'] ?? ''),
            'quantity'      => (int) $data['quantity'],
            'notes'         => trim($data['notes'] ?? ''),
            'marked'        => false,
            'ebay_link'     => trim($data['ebay_link'] ?? ''),
            'website_link'  => trim($data['website_link'] ?? ''),
            'social_media_1' => trim($data['social_media_1'] ?? ''),
            'social_media_2' => trim($data['social_media_2'] ?? ''),
            'social_media_3' => trim($data['social_media_3'] ?? ''),
            'images'        => $this->storeUploadedImages($files, $folderPath),
            'upload_status' => count($files) > 0 ? 'complete' : 'pending',
            'created_at'    => $createdAt,
            'updated_at'    => $createdAt,
        ];

        $this->writeMetadata($folderPath, $metadata);

        return $this->product($folder) ?? array_merge($metadata, [
            'product_name'       => $metadata['folder_name'],
            'path'               => $folderPath,
            'metadata'           => $metadata,
            'created_at_denver'  => $this->formatDenverTimestamp($metadata['created_at']),
            'updated_at_denver'  => $this->formatDenverTimestamp($metadata['updated_at']),
        ]);
    }

    /**
     * Create a product folder by moving pre-uploaded temp images (fast path).
     */
    private function createProductFolderFromTempIds(array $data, array $tempIds): array
    {
        $basePath = $this->basePath();
        File::ensureDirectoryExists($basePath, 0755);

        $folderName = trim($data['folder_name']);
        [$folder, $folderPath] = $this->createUniqueProductDirectory($basePath, $folderName);

        $images    = [];
        $index     = 0;

        foreach ($tempIds as $tempId) {
            if (!preg_match('/^[0-9a-f\-]{36}$/i', $tempId)) {
                continue;
            }

            $tempDir = storage_path('app/' . self::TEMP_DIR . '/' . $tempId);

            if (!is_dir($tempDir)) {
                continue;
            }

            foreach (glob($tempDir . '/*') ?: [] as $tempFile) {
                if (!is_file($tempFile)) {
                    continue;
                }

                $originalExtension = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
                if (!in_array($originalExtension, self::IMAGE_EXTENSIONS, true)) {
                    continue;
                }

                $originalName = basename($tempFile);
                $imageBase    = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'image';
                $filename     = $this->uniqueFilename(
                    $folderPath,
                    sprintf('%02d_%s', $index + 1, $imageBase),
                    $originalExtension
                );

                $destination = $folderPath . DIRECTORY_SEPARATOR . $filename;

                if (!File::move($tempFile, $destination)) {
                    continue; // skip if move failed
                }

                $images[] = [
                    'filename'      => $filename,
                    'original_name' => $originalName,
                    'size'          => filesize($destination) ?: 0,
                    'uploaded_at'   => $this->denverNow()->toIso8601String(),
                ];

                $index++;
            }

            // Clean up temp directory
            File::deleteDirectory($tempDir);
        }

        $createdAt = $this->denverNow()->toIso8601String();
        $metadata  = [
            'folder'        => $folder,
            'folder_name'   => $folderName,
            'condition'     => $data['condition'],
            'folder_type'   => $data['folder_type'] ?? 'Ebay',
            'location'      => trim($data['location'] ?? ''),
            'quantity'      => (int) $data['quantity'],
            'notes'         => trim($data['notes'] ?? ''),
            'marked'        => false,
            'ebay_link'     => trim($data['ebay_link'] ?? ''),
            'website_link'  => trim($data['website_link'] ?? ''),
            'social_media_1' => trim($data['social_media_1'] ?? ''),
            'social_media_2' => trim($data['social_media_2'] ?? ''),
            'social_media_3' => trim($data['social_media_3'] ?? ''),
            'images'        => $images,
            'upload_status' => count($images) > 0 ? 'complete' : 'pending',
            'created_at'    => $createdAt,
            'updated_at'    => $createdAt,
        ];

        $this->writeMetadata($folderPath, $metadata);

        return $this->product($folder) ?? array_merge($metadata, [
            'product_name'      => $metadata['folder_name'],
            'path'              => $folderPath,
            'metadata'          => $metadata,
            'created_at_denver' => $this->formatDenverTimestamp($metadata['created_at']),
            'updated_at_denver' => $this->formatDenverTimestamp($metadata['updated_at']),
        ]);
    }

    private function storeUploadedImages(array $files, string $folderPath, int $offset = 0): array
    {
        $images = [];

        foreach ($files as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
            $extension = preg_replace('/[^a-z0-9]+/', '', $extension) ?: 'jpg';
            $originalBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $imageBase = Str::slug($originalBase) ?: 'image';
            $filename = $this->uniqueFilename(
                $folderPath,
                sprintf('%02d_%s', $offset + $index + 1, $imageBase),
                $extension
            );
            $size = $file->getSize();

            $file->move($folderPath, $filename);

            $images[] = [
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $size,
                'uploaded_at' => $this->denverNow()->toIso8601String(),
            ];
        }

        return $images;
    }

    private function productPayload(array $product): array
    {
        return [
            'folder' => $product['folder'],
            'product_name' => $product['product_name'],
            'folder_name' => $product['folder_name'],
            'condition' => $product['condition'],
            'folder_type' => $product['folder_type'] ?? 'Ebay',
            'location' => $product['location'],
            'quantity' => $product['quantity'],
            'notes' => $product['notes'],
            'marked' => $product['marked'],
            'ebay_link' => $product['ebay_link'] ?? '',
            'website_link' => $product['website_link'] ?? '',
            'social_media_1' => $product['social_media_1'] ?? '',
            'social_media_2' => $product['social_media_2'] ?? '',
            'social_media_3' => $product['social_media_3'] ?? '',
            'images' => $product['images'],
            'upload_status' => $product['upload_status'],
            'created_at' => $product['created_at'],
            'updated_at' => $product['updated_at'],
            'created_at_denver' => $product['created_at_denver'],
            'updated_at_denver' => $product['updated_at_denver'],
        ];
    }

    private function products(): array
    {
        try {
            $products = Cache::get(self::PRODUCTS_CACHE_KEY);

            if (is_array($products)) {
                return $products;
            }

            return Cache::lock(self::PRODUCTS_CACHE_KEY . ':lock', 10)->block(3, function () {
                $products = Cache::get(self::PRODUCTS_CACHE_KEY);

                if (is_array($products)) {
                    return $products;
                }

                $products = $this->productsFromDisk();
                Cache::put(self::PRODUCTS_CACHE_KEY, $products, self::PRODUCTS_CACHE_SECONDS);

                return $products;
            });
        } catch (\Throwable) {
            return $this->productsFromDisk();
        }
    }

    private function productsFromDisk(): array
    {
        $basePath = $this->basePath();
        File::ensureDirectoryExists($basePath, 0755);

        $products = collect(File::directories($basePath))
            ->map(fn ($directory) => $this->productFromDirectory($directory))
            ->filter()
            ->sortByDesc(fn ($product) => strtotime($product['created_at'] ?? '') ?: File::lastModified($product['path']))
            ->values()
            ->all();

        return $products;
    }

    private function statsForProducts(array $products): array
    {
        return [
            'folders' => count($products),
            'images' => collect($products)->sum(fn ($product) => count($product['images'])),
            'quantity' => collect($products)->sum(fn ($product) => (int) $product['quantity']),
            'marked' => collect($products)->filter(fn ($p) => $p['marked'] === true || in_array($p['marked'], ['ebay', 'denver', 'both', 'abid', 'moses'], true))->count(),
        ];
    }

    private function hasIncompleteUploads(?array $products = null): bool
    {
        return collect($products ?? $this->products())->contains(function ($product) {
            return count($product['images'] ?? []) === 0
                || ($product['upload_status'] ?? '') === 'pending';
        });
    }

    private function product(string $folder): ?array
    {
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $folder)) {
            return null;
        }

        $directory = $this->basePath() . DIRECTORY_SEPARATOR . $folder;

        if (!File::isDirectory($directory)) {
            return null;
        }

        return $this->productFromDirectory($directory);
    }

    private function productFromDirectory(string $directory): ?array
    {
        if (!File::isDirectory($directory)) {
            return null;
        }

        $folder = basename($directory);
        $metadataPath = $directory . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];

        if (File::isFile($metadataPath)) {
            $decoded = json_decode(File::get($metadataPath), true);
            $metadata = is_array($decoded) ? $decoded : [];
        }

        $images = collect($metadata['images'] ?? [])
            ->filter(fn ($image) => is_array($image) && isset($image['filename']))
            ->map(fn ($image) => [
                'filename' => basename($image['filename']),
                'original_name' => $image['original_name'] ?? basename($image['filename']),
                'size' => $image['size'] ?? null,
                'uploaded_at' => $image['uploaded_at'] ?? null,
            ])
            ->values();

        $knownImages = $images->pluck('filename')->all();

        foreach (File::files($directory) as $file) {
            $extension = strtolower($file->getExtension());

            if (!in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                continue;
            }

            if (in_array($file->getFilename(), $knownImages, true)) {
                continue;
            }

            $images->push([
                'filename' => $file->getFilename(),
                'original_name' => $file->getFilename(),
                'size' => $file->getSize(),
                'uploaded_at' => date('c', $file->getMTime()),
            ]);
        }

        $createdAt = $metadata['created_at'] ?? date('c', File::lastModified($directory));
        $updatedAt = $metadata['updated_at'] ?? $createdAt;

        $condition = $metadata['condition'] ?? '';
        $displayName = $metadata['folder_name'] ?? ($metadata['product_name'] ?? Str::headline(str_replace(['_', '-'], ' ', $folder)));

        return [
            'folder' => $folder,
            'product_name' => $displayName,
            'folder_name' => $metadata['folder_name'] ?? $displayName,
            'condition' => $condition,
            'folder_type' => $metadata['folder_type'] ?? 'Ebay',
            'location' => $metadata['location'] ?? '',
            'quantity' => (int) ($metadata['quantity'] ?? 0),
            'notes' => $metadata['notes'] ?? '',
            'marked' => $metadata['marked'] ?? false,
            'ebay_link' => $metadata['ebay_link'] ?? '',
            'website_link' => $metadata['website_link'] ?? '',
            'social_media_1' => $metadata['social_media_1'] ?? '',
            'social_media_2' => $metadata['social_media_2'] ?? '',
            'social_media_3' => $metadata['social_media_3'] ?? '',
            'images' => $images->all(),
            'upload_status' => $metadata['upload_status'] ?? (count($images) > 0 ? 'complete' : ''),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'created_at_denver' => $this->formatDenverTimestamp($createdAt),
            'updated_at_denver' => $this->formatDenverTimestamp($updatedAt),
            'metadata' => array_merge($metadata, [
                'folder' => $folder,
                'images' => $images->all(),
            ]),
            'path' => $directory,
        ];
    }

    private function uploadedImages(Request $request): array
    {
        $files = $request->file('images', []);
        $files = is_array($files) ? $files : [$files];

        return array_values(array_filter(
            $this->flattenFiles($files),
            fn ($file) => $file instanceof UploadedFile && $file->isValid()
        ));
    }

    private function flattenFiles(array $files): array
    {
        $flat = [];

        foreach ($files as $file) {
            if (is_array($file)) {
                $flat = array_merge($flat, $this->flattenFiles($file));
                continue;
            }

            $flat[] = $file;
        }

        return $flat;
    }

    private function bestImageMatch(array $product, string $searchHash): ?array
    {
        $best = null;

        foreach ($product['images'] as $image) {
            $imagePath = $product['path'] . DIRECTORY_SEPARATOR . $image['filename'];

            if (!File::isFile($imagePath)) {
                continue;
            }

            $hash = $this->perceptualHash($imagePath);

            if (!$hash) {
                continue;
            }

            $distance = $this->hammingDistance($searchHash, $hash);

            if (!$best || $distance < $best['distance']) {
                $best = [
                    'filename' => $image['filename'],
                    'original_name' => $image['original_name'] ?? $image['filename'],
                    'distance' => $distance,
                    'score' => max(0, (int) round(((64 - $distance) / 64) * 100)),
                ];
            }
        }

        return $best;
    }

    private function perceptualHash(string $path): ?string
    {
        $source = $this->imageResource($path);

        if (!$source) {
            return null;
        }

        $width = 9;
        $height = 8;
        $resized = imagecreatetruecolor($width, $height);

        if (!$resized) {
            imagedestroy($source);
            return null;
        }

        imagecopyresampled(
            $resized,
            $source,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($source),
            imagesy($source)
        );

        $bits = '';

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width - 1; $x++) {
                $left = $this->grayValue($resized, $x, $y);
                $right = $this->grayValue($resized, $x + 1, $y);
                $bits .= $left > $right ? '1' : '0';
            }
        }

        imagedestroy($resized);
        imagedestroy($source);

        return $bits;
    }

    private function imageResource(string $path)
    {
        $mime = File::mimeType($path);

        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    private function grayValue($image, int $x, int $y): int
    {
        $rgb = imagecolorat($image, $x, $y);
        $red = ($rgb >> 16) & 0xFF;
        $green = ($rgb >> 8) & 0xFF;
        $blue = $rgb & 0xFF;

        return (int) round(($red * 0.299) + ($green * 0.587) + ($blue * 0.114));
    }

    private function hammingDistance(string $first, string $second): int
    {
        $distance = 0;
        $length = min(strlen($first), strlen($second));

        for ($i = 0; $i < $length; $i++) {
            if ($first[$i] !== $second[$i]) {
                $distance++;
            }
        }

        return $distance + abs(strlen($first) - strlen($second));
    }

    private function uniqueFolderName(string $productName): string
    {
        $slug = Str::slug($productName) ?: 'product';
        $base = $this->denverNow()->format('Ymd_His_u') . '_' . $slug;
        $folder = $base;
        $counter = 2;

        while (File::exists($this->basePath() . DIRECTORY_SEPARATOR . $folder)) {
            $folder = $base . '_' . $counter;
            $counter++;
        }

        return $folder;
    }

    private function createUniqueProductDirectory(string $basePath, string $folderName): array
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $folder = $this->uniqueFolderName($folderName);
            $folderPath = $basePath . DIRECTORY_SEPARATOR . $folder;

            if (File::makeDirectory($folderPath, 0755, true, true)) {
                return [$folder, $folderPath];
            }
        }

        throw new \RuntimeException('Could not create a unique eBay product folder.');
    }

    private function uniqueFilename(string $directory, string $base, string $extension): string
    {
        $filename = $base . '.' . $extension;
        $counter = 2;

        while (File::exists($directory . DIRECTORY_SEPARATOR . $filename)) {
            $filename = $base . '_' . $counter . '.' . $extension;
            $counter++;
        }

        return $filename;
    }

    private function writeMetadata(string $folderPath, array $metadata): void
    {
        File::put(
            $folderPath . DIRECTORY_SEPARATOR . 'metadata.json',
            json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->forgetProductsCache();
    }

    private function basePath(): string
    {
        return public_path('ebay');
    }

    private function denverNow(): CarbonImmutable
    {
        return CarbonImmutable::now(self::DENVER_TIMEZONE);
    }

    private function formatDenverTimestamp(?string $timestamp): string
    {
        if (!$timestamp) {
            return '-';
        }

        try {
            return CarbonImmutable::parse($timestamp)
                ->setTimezone(self::DENVER_TIMEZONE)
                ->format('M j, Y g:i A T');
        } catch (\Throwable) {
            return '-';
        }
    }

    private function forgetProductsCache(): void
    {
        try {
            Cache::forget(self::PRODUCTS_CACHE_KEY);
        } catch (\Throwable) {
            //
        }
    }

    private function noIndex(string $view, array $data = []): Response
    {
        return response()
            ->view($view, $data)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
