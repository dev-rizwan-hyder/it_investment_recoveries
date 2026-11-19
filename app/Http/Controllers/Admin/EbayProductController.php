<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class EbayProductController extends Controller
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'];
    private const DENVER_TIMEZONE = 'America/Denver';
    private const TEMP_DIR = 'ebay-product-temp';
    private const BASE_DIR = 'ebay-product';

    public function index(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();
        $status = in_array($status, ['done', 'remaining', 'ebay', 'denver', 'both', 'abid', 'moses', 'sold'], true) ? $status : '';
        $folderTypeSearch = $request->string('folder_type')->toString();
        $folderTypeSearch = in_array($folderTypeSearch, ['Ebay', 'Denver', 'Both'], true) ? $folderTypeSearch : '';
        $userSearch = $request->string('user')->toString();
        $userSearch = in_array($userSearch, ['Abid', 'Moses'], true) ? $userSearch : '';
        $dateRange = $request->string('date_range')->toString();
        $validRanges = ['today', 'yesterday', 'week', 'this_week', 'last_week', 'month', 'this_month', 'last_month', '60days', '90days', 'this_year', 'last_year', 'custom'];
        $dateRange = in_array($dateRange, $validRanges, true) ? $dateRange : '';
        $startDate = $request->string('start_date')->toString();
        $endDate = $request->string('end_date')->toString();

        $allProducts = $this->productsFromDisk();
        $products = $allProducts;

        // Apply filters
        // Auto-detect: if the search term looks like a URL, search ebay_link; otherwise search by name/folder
        if ($search !== '') {
            $needle = strtolower($search);
            $isLinkSearch = str_starts_with($needle, 'http') || str_starts_with($needle, 'www.');
            $products = array_values(array_filter($products, function ($product) use ($needle, $isLinkSearch) {
                if ($isLinkSearch) {
                    $haystack = strtolower($product['ebay_link'] ?? '');
                } else {
                    $haystack = strtolower(implode(' ', [
                        $product['folder_name'] ?? '',
                        $product['folder'] ?? '',
                        $product['location'] ?? '',
                        $product['folder_type'] ?? 'Ebay',
                        $product['user'] ?? '',
                    ]));
                }
                return str_contains($haystack, $needle);
            }));
        }

        if ($status !== '') {
            $products = array_values(array_filter($products, function ($product) use ($status) {
                $marked = $product['marked'] ?? false;
                $sold   = $product['sold']   ?? false;
                if ($status === 'sold') {
                    return $sold === true;
                }
                if ($status === 'remaining') {
                    return ($marked === false || $marked === 'remaining') && !$sold;
                }
                if ($status === 'done') {
                    return $marked === true || in_array($marked, ['ebay', 'denver', 'both', 'abid', 'moses'], true);
                }
                if ($status === 'ebay' || $status === 'abid') {
                    return $marked === 'ebay' || $marked === 'abid';
                }
                if ($status === 'denver' || $status === 'moses') {
                    return $marked === 'denver' || $marked === 'moses';
                }
                if ($status === 'both') {
                    return $marked === 'both' || $marked === true;
                }
                return true;
            }));
        }

        if ($dateRange !== '') {
            $now = $this->denverNow();
            $startCutoff = null;
            $endCutoff = null;

            switch ($dateRange) {
                case 'today':
                    $startCutoff = $now->startOfDay();
                    $endCutoff = $now->endOfDay();
                    break;
                case 'yesterday':
                    $startCutoff = $now->subDay()->startOfDay();
                    $endCutoff = $now->subDay()->endOfDay();
                    break;
                case 'week':
                case 'this_week':
                    $startCutoff = $now->startOfWeek();
                    $endCutoff = $now->endOfWeek();
                    break;
                case 'last_week':
                    $startCutoff = $now->subWeek()->startOfWeek();
                    $endCutoff = $now->subWeek()->endOfWeek();
                    break;
                case 'month':
                case 'this_month':
                    $startCutoff = $now->startOfMonth();
                    $endCutoff = $now->endOfMonth();
                    break;
                case 'last_month':
                    $startCutoff = $now->subMonth()->startOfMonth();
                    $endCutoff = $now->subMonth()->endOfMonth();
                    break;
                case '60days':
                    $startCutoff = $now->subDays(60)->startOfDay();
                    break;
                case '90days':
                    $startCutoff = $now->subDays(90)->startOfDay();
                    break;
                case 'this_year':
                    $startCutoff = $now->startOfYear();
                    $endCutoff = $now->endOfYear();
                    break;
                case 'last_year':
                    $startCutoff = $now->subYear()->startOfYear();
                    $endCutoff = $now->subYear()->endOfYear();
                    break;
                case 'custom':
                    if ($startDate !== '') {
                        try {
                            $startCutoff = \Carbon\Carbon::parse($startDate)->startOfDay();
                        } catch (\Exception $e) {}
                    }
                    if ($endDate !== '') {
                        try {
                            $endCutoff = \Carbon\Carbon::parse($endDate)->endOfDay();
                        } catch (\Exception $e) {}
                    }
                    break;
            }

            if ($startCutoff || $endCutoff) {
                $products = array_values(array_filter($products, function ($product) use ($startCutoff, $endCutoff) {
                    $createdAt = $product['created_at'] ?? null;
                    if (!$createdAt) return false;
                    try {
                        $pDate = \Carbon\Carbon::parse($createdAt);
                        if ($startCutoff && $pDate->lt($startCutoff)) {
                            return false;
                        }
                        if ($endCutoff && $pDate->gt($endCutoff)) {
                            return false;
                        }
                        return true;
                    } catch (\Exception $e) {
                        return false;
                    }
                }));
            }
        }

        if ($userSearch !== '') {
            $products = array_values(array_filter($products, function ($product) use ($userSearch) {
                return ($product['user'] ?? '') === $userSearch;
            }));
        }

        if ($folderTypeSearch !== '') {
            $products = array_values(array_filter($products, function ($product) use ($folderTypeSearch) {
                $type = $product['folder_type'] ?? 'Ebay';
                if ($folderTypeSearch === 'Both') {
                    return in_array($type, ['Ebay', 'Denver', 'Both'], true);
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

        $stats = $this->statsForProducts($products);

        // Paginate results
        $currentPage = (int) ($request->input('page') ?: Paginator::resolveCurrentPage('page'));
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 10;
        }

        $total = count($products);
        $currentItems = array_slice($products, ($currentPage - 1) * $perPage, $perPage);

        $paginatedProducts = new LengthAwarePaginator(
            $currentItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.ebay.partials.products-list', ['products' => $paginatedProducts])->render(),
                'pagination_html' => view('admin.ebay.partials.pagination', ['products' => $paginatedProducts])->render(),
                'stats' => $stats,
            ]);
        }

        return view('admin.ebay.index', [
            'products' => $paginatedProducts,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
            'folderTypeSearch' => $folderTypeSearch,
            'userSearch' => $userSearch,
            'dateRange' => $dateRange,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Search product folders by image perceptual similarity.
     */
    public function imageSearch(Request $request)
    {
        $fileKey = $request->hasFile('image') ? 'image' : 'search_image';

        $request->validate([
            $fileKey => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/octet-stream',
                'max:20480',
            ],
        ]);

        $searchImage = $request->file($fileKey);
        $searchRealPath = $searchImage->getRealPath();

        // Compute search image hash and MD5 ONCE for performance
        $searchHash = $this->perceptualHash($searchRealPath);
        $searchMD5 = $searchRealPath ? (md5_file($searchRealPath) ?: '') : '';

        if (!$searchHash) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This image format could not be processed for comparison. Please use JPG, PNG, WEBP, or GIF.',
                ], 422);
            }
            return back()->withErrors(['search_image' => 'This image format could not be processed for comparison. Please use JPG, PNG, WEBP, or GIF.']);
        }

        $allProducts = $this->productsFromDisk();

        $products = collect($allProducts)
            ->map(function ($product) use ($searchHash, $searchMD5) {
                $match = $this->bestImageMatch($product, $searchHash, $searchMD5);
                if (!$match) {
                    return null;
                }
                $product['similarity_match'] = $match;
                return $product;
            })
            ->filter(fn ($product) => $product && $product['similarity_match']['score'] >= 100)
            ->sortBy(fn ($product) => $product['similarity_match']['distance'])
            ->values()
            ->all();

        $stats = $this->statsForProducts($products);

        $currentPage = (int) ($request->input('page') ?: Paginator::resolveCurrentPage('page'));
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $perPage = 10;
        $total = count($products);
        $currentItems = array_slice($products, ($currentPage - 1) * $perPage, $perPage);

        $paginatedProducts = new LengthAwarePaginator(
            $currentItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'success' => true,
                'html' => view('admin.ebay.partials.products-list', ['products' => $paginatedProducts])->render(),
                'pagination_html' => view('admin.ebay.partials.pagination', ['products' => $paginatedProducts])->render(),
                'stats' => $stats,
                'count' => $total,
                'matched_count' => $total,
                'image_name' => $searchImage->getClientOriginalName(),
            ]);
        }

        return view('admin.ebay.index', [
            'products' => $paginatedProducts,
            'stats' => $stats,
            'search' => '',
            'status' => '',
            'folderTypeSearch' => '',
            'userSearch' => '',
            'dateRange' => '',
            'startDate' => '',
            'endDate' => '',
            'imageSearch' => true,
            'imageSearchName' => $searchImage->getClientOriginalName(),
        ]);
    }

    private function bestImageMatch(array $product, string $searchHash, string $searchMD5 = ''): ?array
    {
        $best = null;

        foreach ($product['images'] as $image) {
            $imagePath = $product['path'] . DIRECTORY_SEPARATOR . $image['filename'];

            if (!File::isFile($imagePath)) {
                continue;
            }

            // Retrieve cached image hashes (MD5 & dHash) to avoid disk re-decoding
            $cachedHashes = $this->getImageHashes($imagePath);

            // Check exact file content hash match (instant 100% match)
            if ($searchMD5 !== '' && $cachedHashes['md5'] !== '' && $searchMD5 === $cachedHashes['md5']) {
                return [
                    'filename' => $image['filename'],
                    'original_name' => $image['original_name'] ?? $image['filename'],
                    'distance' => 0,
                    'score' => 100,
                ];
            }

            $hash = $cachedHashes['dhash'];

            if (!$hash) {
                continue;
            }

            $distance = $this->hammingDistance($searchHash, $hash);
            $score = max(0, (int) round(((64 - $distance) / 64) * 100));

            if (!$best || $distance < $best['distance']) {
                $best = [
                    'filename' => $image['filename'],
                    'original_name' => $image['original_name'] ?? $image['filename'],
                    'distance' => $distance,
                    'score' => $score,
                ];
                if ($score >= 100) {
                    break;
                }
            }
        }

        return $best;
    }

    private function getImageHashes(string $imagePath): array
    {
        static $hashCache = [];

        if (isset($hashCache[$imagePath])) {
            return $hashCache[$imagePath];
        }

        $mtime = File::lastModified($imagePath);
        $cacheKey = 'ebay_img_hash_' . md5($imagePath . '_' . $mtime);

        $hashes = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$hashes || !is_array($hashes)) {
            $hashes = [
                'md5' => md5_file($imagePath) ?: '',
                'dhash' => $this->perceptualHash($imagePath) ?: '',
            ];
            \Illuminate\Support\Facades\Cache::put($cacheKey, $hashes, 86400 * 30);
        }

        $hashCache[$imagePath] = $hashes;
        return $hashes;
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

    /**
     * Store temporary uploaded images to allow instant creation later.
     */
    public function tempUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif',
                'max:20480', // 20MB max per image
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image format or size.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $files = $request->file('images', []);
        $files = is_array($files) ? $files : [$files];
        $uploaded = [];

        $tempBase = public_path(self::TEMP_DIR);
        File::ensureDirectoryExists($tempBase, 0755);

        foreach ($files as $file) {
            if (!$file->isValid()) continue;

            $tempId = Str::uuid()->toString();
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
            $originalName = $file->getClientOriginalName();
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            $targetDir = $tempBase . DIRECTORY_SEPARATOR . $tempId;
            File::ensureDirectoryExists($targetDir, 0755);
            
            $file->move($targetDir, $filename);

            $uploaded[] = [
                'temp_id' => $tempId,
                'filename' => $filename,
                'original_name' => $originalName,
                'size' => File::size($targetDir . DIRECTORY_SEPARATOR . $filename),
                'url' => asset(self::TEMP_DIR . '/' . $tempId . '/' . $filename),
            ];
        }

        return response()->json([
            'success' => true,
            'uploaded' => $uploaded,
        ]);
    }

    /**
     * Create folder and save metadata + move images.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'folder_name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', 'in:New,Used,Refurbished,Open Box'],
            'folder_type' => ['required', 'string', 'in:Ebay,Denver,Both'],
            'location' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'marked' => ['nullable', 'string', 'in:remaining,ebay,denver,both,abid,moses,0,1'],
            'user' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric'],
            'value' => ['nullable', 'numeric'],
            'estimated_value' => ['nullable', 'numeric'],
            'temp_ids' => ['nullable', 'array'],
            'temp_ids.*' => ['string'],
            'ebay_link' => ['nullable', 'string', 'max:2048'],
            'website_link' => ['nullable', 'string', 'max:2048'],
            'social_media_1' => ['nullable', 'string', 'max:2048'],
            'social_media_2' => ['nullable', 'string', 'max:2048'],
            'social_media_3' => ['nullable', 'string', 'max:2048'],
        ]);

        $basePath = $this->basePath();
        File::ensureDirectoryExists($basePath, 0755);

        $folderName = trim($data['folder_name']);
        
        // Generate unique folder name
        $slug = Str::slug($folderName) ?: 'product';
        $baseFolder = $this->denverNow()->format('Ymd_His') . '_' . $slug;
        $folder = $baseFolder;
        $counter = 2;
        while (File::exists($basePath . DIRECTORY_SEPARATOR . $folder)) {
            $folder = $baseFolder . '_' . $counter;
            $counter++;
        }

        $folderPath = $basePath . DIRECTORY_SEPARATOR . $folder;
        File::makeDirectory($folderPath, 0755, true);

        $images = [];
        $index = 0;
        
        if ($request->hasFile('images')) {
            $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];
            foreach ($files as $file) {
                if (!$file->isValid()) continue;

                $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
                if (!in_array($ext, self::IMAGE_EXTENSIONS, true)) continue;

                $originalName = $file->getClientOriginalName();
                $imageBase = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'image';
                $destFilename = sprintf('%02d_%s.%s', $index + 1, $imageBase, $ext);
                
                $file->move($folderPath, $destFilename);

                $images[] = [
                    'filename' => $destFilename,
                    'original_name' => $originalName,
                    'size' => File::size($folderPath . DIRECTORY_SEPARATOR . $destFilename),
                    'uploaded_at' => $this->denverNow()->toIso8601String(),
                ];
                $index++;
            }
        }

        $markedInput = $request->input('marked', 'remaining');
        if (in_array($markedInput, ['1', 'true', true], true)) {
            $markedInput = 'both';
        } elseif (in_array($markedInput, ['0', 'false', false, null, '', 'remaining'], true)) {
            $markedInput = 'remaining';
        }

        $createdAt = $this->denverNow()->toIso8601String();
        $metadata = [
            'folder' => $folder,
            'folder_name' => $folderName,
            'condition' => $data['condition'],
            'folder_type' => $data['folder_type'],
            'location' => trim($data['location'] ?? ''),
            'quantity' => (int) $data['quantity'],
            'notes' => trim($data['notes'] ?? ''),
            'marked' => $markedInput,
            'user' => $data['user'] ?? '',
            'serial_number' => trim($data['serial_number'] ?? ''),
            'weight' => $data['weight'] ?? '',
            'value' => $data['value'] ?? $data['estimated_value'] ?? '',
            'estimated_value' => $data['value'] ?? $data['estimated_value'] ?? '',
            'ebay_link' => trim($data['ebay_link'] ?? ''),
            'website_link' => trim($data['website_link'] ?? ''),
            'social_media_1' => trim($data['social_media_1'] ?? ''),
            'social_media_2' => trim($data['social_media_2'] ?? ''),
            'social_media_3' => trim($data['social_media_3'] ?? ''),
            'images' => $images,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        $this->writeMetadata($folderPath, $metadata);

        $product = $this->productFromDirectory($folderPath);

        $this->clearStatsCache();

        return response()->json([
            'success' => true,
            'message' => 'Product folder created successfully.',
            'product' => $product,
            'html' => view('admin.ebay.partials.product-card', ['product' => $product])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Update product details.
     */
    public function update(Request $request, string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $data = $request->validate([
            'folder_name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', 'in:New,Used,Refurbished,Open Box'],
            'folder_type' => ['required', 'string', 'in:Ebay,Denver,Both'],
            'location' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'marked' => ['nullable', 'string', 'in:remaining,ebay,denver,both,abid,moses,0,1'],
            'user' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric'],
            'value' => ['nullable', 'numeric'],
            'estimated_value' => ['nullable', 'numeric'],
            'ebay_link' => ['nullable', 'string', 'max:2048'],
            'website_link' => ['nullable', 'string', 'max:2048'],
            'social_media_1' => ['nullable', 'string', 'max:2048'],
            'social_media_2' => ['nullable', 'string', 'max:2048'],
            'social_media_3' => ['nullable', 'string', 'max:2048'],
        ]);

        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }

        $markedInput = $request->input('marked', 'remaining');
        if (in_array($markedInput, ['1', 'true', true], true)) {
            $markedInput = 'both';
        } elseif (in_array($markedInput, ['0', 'false', false, null, '', 'remaining'], true)) {
            $markedInput = 'remaining';
        }

        $metadata['folder_name'] = trim($data['folder_name']);
        $metadata['condition'] = $data['condition'];
        $metadata['folder_type'] = $data['folder_type'];
        $metadata['location'] = trim($data['location'] ?? '');
        $metadata['quantity'] = (int) $data['quantity'];
        $metadata['notes'] = trim($data['notes'] ?? '');
        $metadata['marked'] = $markedInput;
        $metadata['user'] = $data['user'] ?? '';
        $metadata['serial_number'] = trim($data['serial_number'] ?? '');
        $metadata['weight'] = $data['weight'] ?? '';
        $metadata['ebay_link'] = trim($data['ebay_link'] ?? '');
        $metadata['website_link'] = trim($data['website_link'] ?? '');
        $metadata['social_media_1'] = trim($data['social_media_1'] ?? '');
        $metadata['social_media_2'] = trim($data['social_media_2'] ?? '');
        $metadata['social_media_3'] = trim($data['social_media_3'] ?? '');
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($folderPath, $metadata);

        $product = $this->productFromDirectory($folderPath);

        $this->clearStatsCache();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product,
            'html' => view('admin.ebay.partials.product-card', ['product' => $product])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Toggle the marked (Done) status.
     */
    public function toggleMark(string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }

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

        $this->writeMetadata($folderPath, $metadata);

        $product = $this->productFromDirectory($folderPath);

        $this->clearStatsCache();

        return response()->json([
            'success' => true,
            'marked' => $newMarked,
            'html' => view('admin.ebay.partials.product-card', ['product' => $product])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Toggle the blinking red alert status.
     */
    public function toggleBlink(Request $request, string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }

        if ($request->has('blinking')) {
            $newBlinking = filter_var($request->input('blinking'), FILTER_VALIDATE_BOOLEAN);
        } else {
            $newBlinking = empty($metadata['blinking']);
        }

        $metadata['blinking'] = $newBlinking;
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($folderPath, $metadata);

        $product = $this->productFromDirectory($folderPath);

        return response()->json([
            'success' => true,
            'blinking' => $newBlinking,
            'html' => view('admin.ebay.partials.product-card', ['product' => $product])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Delete product folder.
     */
    public function destroy(string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $trashPath = public_path('ebay-product-trash');
        File::ensureDirectoryExists($trashPath, 0755);
        $trashedFolderPath = $trashPath . DIRECTORY_SEPARATOR . basename($folder) . '_' . time();
        File::moveDirectory($folderPath, $trashedFolderPath, true);
        File::put($trashedFolderPath . DIRECTORY_SEPARATOR . '.trashed_at', now()->toDateTimeString());

        $this->clearStatsCache();

        return response()->json([
            'success' => true,
            'message' => 'Product folder moved to trash successfully.',
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Delete single image inside product folder.
     */
    public function deleteImage(string $folder, string $filename)
    {
        $folder = basename($folder);
        $filename = basename($filename);
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . $folder;
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $filename;

        if (!File::isDirectory($folderPath) || !File::isFile($filePath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        File::delete($filePath);

        // Update metadata.json
        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }

        $existingImages = $metadata['images'] ?? [];
        $metadata['images'] = array_values(array_filter($existingImages, function($img) use ($filename) {
            return basename($img['filename']) !== $filename;
        }));
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($folderPath, $metadata);

        $product = $this->productFromDirectory($folderPath);

        $this->clearStatsCache();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
            'product' => $product,
            'html' => view('admin.ebay.partials.product-card', ['product' => $product])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Upload additional images to an existing product folder (supports temp ids).
     */
    public function uploadImages(Request $request, string $folder)
    {
        $folder = basename($folder);
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . $folder;

        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['file', 'max:20480'],
        ]);

        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }

        $images = $metadata['images'] ?? [];
        $index = count($images);
        
        if ($request->hasFile('images')) {
            $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];
            foreach ($files as $file) {
                if (!$file->isValid()) continue;

                $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
                if (!in_array($ext, self::IMAGE_EXTENSIONS, true)) continue;

                $originalName = $file->getClientOriginalName();
                $imageBase = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'image';
                $destFilename = sprintf('%02d_%s.%s', $index + 1, $imageBase, $ext);
                
                $file->move($folderPath, $destFilename);

                $images[] = [
                    'filename' => $destFilename,
                    'original_name' => $originalName,
                    'size' => File::size($folderPath . DIRECTORY_SEPARATOR . $destFilename),
                    'uploaded_at' => $this->denverNow()->toIso8601String(),
                ];
                $index++;
            }
        }

        $metadata['images'] = $images;
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();

        $this->writeMetadata($folderPath, $metadata);

        $product = $this->productFromDirectory($folderPath);

        $this->clearStatsCache();

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully.',
            'product' => $product,
            'html' => view('admin.ebay.partials.product-card', ['product' => $product])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    public function prepareToInventory(string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $product = $this->productFromDirectory($folderPath);

        $imageUrl = fn ($filename) => asset('ebay-product/' . rawurlencode($product['folder']) . '/' . rawurlencode($filename));

        $imagesList = array_map(function ($img) use ($imageUrl) {
            return [
                'filename' => $img['filename'],
                'url' => $imageUrl($img['filename']),
            ];
        }, $product['images'] ?? []);

        $productName = $product['product_name'] ?? '';
        $userAssigned = $product['user'] ?? '';
        if ($userAssigned && strtolower(trim($productName)) === strtolower(trim($userAssigned))) {
            $productName = '';
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $product['folder'],
                'folder' => $product['folder'],
                'product_name' => $product['product_name'] ?? '',
                'condition' => $product['condition'] ?? '',
            ],
            'inventoryData' => [
                'brand_category' => $productName,
                'put_away_location' => $product['location'] ?? '',
                'quantity' => $product['quantity'] ?? 1,
                'notes' => $product['notes'] ?? '',
                'condition' => $product['condition'] ?? '',
                'price' => $product['value'] ?? $product['estimated_value'] ?? 0,
                'images' => $imagesList,
            ]
        ]);
    }

    public function saveToInventory(Request $request, string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $product = $this->productFromDirectory($folderPath);
        $selectedQty = (int) $request->input('quantity', 0);

        if ($selectedQty <= 0) {
            return response()->json(['error' => 'Quantity must be greater than 0.'], 422);
        }

        if ($selectedQty > $product['quantity']) {
            return response()->json(['error' => "Only {$product['quantity']} item(s) are available."], 422);
        }

        try {
            $repairPhotosPaths = [];
            
            if ($request->hasFile('repair_photos')) {
                foreach ($request->file('repair_photos') as $photo) {
                    if ($photo && $photo->isValid()) {
                        try {
                            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                            $photo->move(public_path('repair'), $filename);
                            $repairPhotosPaths[] = 'repair/' . $filename;
                        } catch (\Exception $e) {
                            \Log::warning('Failed to upload repair photo: ' . $e->getMessage());
                        }
                    }
                }
            } else {
                // Include images from the Ebay product folder only if no custom photos submitted
                foreach ($product['images'] as $img) {
                    $sourcePath = $folderPath . DIRECTORY_SEPARATOR . $img['filename'];
                    if (File::isFile($sourcePath)) {
                        $newFilename = time() . '_' . uniqid() . '.' . pathinfo($img['filename'], PATHINFO_EXTENSION);
                        $destPath = public_path('repair') . DIRECTORY_SEPARATOR . $newFilename;
                        File::ensureDirectoryExists(public_path('repair'), 0755);
                        File::copy($sourcePath, $destPath);
                        $repairPhotosPaths[] = 'repair/' . $newFilename;
                    }
                }
            }

            $inventoryRecord = \App\Models\Inventory::create([
                'name' => $request->input('brand_category') ?: $product['product_name'],
                'intake_type' => 'Ebay Product',
                'repair_photos' => $repairPhotosPaths,
                'condition_status' => $product['condition'],
                'technician' => $request->input('technician', ''),
                'customer_phone' => $request->input('customer_phone', ''),
                'customer_email' => $request->input('customer_email', ''),
                'device_type' => $request->input('device_type', ''),
                'category' => $request->input('category', ''),
                'sub_category' => $request->input('sub_category', ''),
                'sub_sub_category' => $request->input('sub_sub_category', ''),
                'category_id' => $request->input('category_id'),
                'sub_category_id' => $request->input('sub_category_id'),
                'sub_sub_category_id' => $request->input('sub_sub_category_id'),
                'brand_category' => $request->input('brand_category', ''),
                'price' => $request->input('price', 0),
                'quantity' => $selectedQty,
                'lot_info' => $request->input('lot_info', ''),
                'serial_number' => $request->input('serial_number', ''),
                'status' => $request->input('status', 'Pending'),
                'pallet_info' => $request->input('pallet_info', ''),
                'device_specs' => $request->input('device_specs', ''),
                'operating_system' => $request->input('operating_system', ''),
                'notes' => $request->input('notes', $product['notes']),
                'put_away_location' => $request->input('put_away_location', $product['location']),
                'barcode' => $request->input('barcode', ''),
            ]);

            $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
            $metadata = [];
            if (File::isFile($metadataPath)) {
                $metadata = json_decode(File::get($metadataPath), true) ?: [];
            }
            $metadata['quantity'] = max(0, $product['quantity'] - $selectedQty);
            $metadata['inventory_id'] = $inventoryRecord->id;
            $metadata['updated_at'] = $this->denverNow()->toIso8601String();
            $this->writeMetadata($folderPath, $metadata);

            $this->clearStatsCache();

            return response()->json([
                'success' => true,
                'message' => "Item successfully added to Inventory!",
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark an inventory item as sold (set quantity to 0).
     */
    public function markAsSold(string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $product = $this->productFromDirectory($folderPath);
        $inventoryId = $product['inventory_id'] ?? null;

        if (!$inventoryId) {
            return response()->json([
                'success' => false,
                'message' => 'This item has not been added to inventory yet. Please add it to inventory first.',
            ], 422);
        }

        $inventoryItem = \App\Models\Inventory::find($inventoryId);
        if (!$inventoryItem) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory record not found. It may have been deleted.',
            ], 404);
        }

        $inventoryItem->quantity = 0;
        $inventoryItem->reuse_quantity = 0;
        $inventoryItem->status = 'Sold';
        $inventoryItem->save();

        // Save sold status and timestamp to metadata
        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }
        $metadata['sold']    = true;
        $metadata['sold_at'] = $this->denverNow()->toIso8601String();
        $metadata['updated_at'] = $metadata['sold_at'];
        $this->writeMetadata($folderPath, $metadata);

        $this->clearStatsCache();

        $updatedProduct = $this->productFromDirectory($folderPath);

        return response()->json([
            'success' => true,
            'message' => 'Item marked as sold. Inventory quantity set to 0.',
            'html' => view('admin.ebay.partials.product-card', ['product' => $updatedProduct])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /**
     * Delete the linked inventory record for this product folder.
     */
    public function destroyInventory(string $folder)
    {
        $folderPath = $this->basePath() . DIRECTORY_SEPARATOR . basename($folder);
        if (!File::isDirectory($folderPath)) {
            return response()->json(['success' => false, 'message' => 'Folder not found.'], 404);
        }

        $product = $this->productFromDirectory($folderPath);
        $inventoryId = $product['inventory_id'] ?? null;

        if (!$inventoryId) {
            return response()->json([
                'success' => false,
                'message' => 'No inventory record is linked to this product.',
            ], 422);
        }

        $inventoryItem = \App\Models\Inventory::find($inventoryId);
        if ($inventoryItem) {
            $inventoryItem->delete();
        }

        // Clear inventory_id from metadata so the card resets
        $metadataPath = $folderPath . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];
        if (File::isFile($metadataPath)) {
            $metadata = json_decode(File::get($metadataPath), true) ?: [];
        }
        unset($metadata['inventory_id']);
        $metadata['updated_at'] = $this->denverNow()->toIso8601String();
        $this->writeMetadata($folderPath, $metadata);

        $this->clearStatsCache();

        $updatedProduct = $this->productFromDirectory($folderPath);

        return response()->json([
            'success' => true,
            'message' => 'Inventory record deleted successfully.',
            'html' => view('admin.ebay.partials.product-card', ['product' => $updatedProduct])->render(),
            'stats' => $this->fastStats(),
        ]);
    }

    /* Helper Methods */

    private function basePath(): string
    {
        return public_path(self::BASE_DIR);
    }

    private function writeMetadata(string $folderPath, array $metadata): void
    {
        File::put(
            $folderPath . DIRECTORY_SEPARATOR . 'metadata.json',
            json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function productsFromDisk(): array
    {
        $basePath = $this->basePath();
        File::ensureDirectoryExists($basePath, 0755);

        return collect(File::directories($basePath))
            ->map(fn ($directory) => $this->productFromDirectory($directory))
            ->filter()
            ->sortByDesc(fn ($product) => strtotime($product['created_at'] ?? '') ?: File::lastModified($product['path']))
            ->values()
            ->all();
    }

    private function productFromDirectory(string $directory): ?array
    {
        if (!File::isDirectory($directory)) return null;

        $folder = basename($directory);
        $metadataPath = $directory . DIRECTORY_SEPARATOR . 'metadata.json';
        $metadata = [];

        if (File::isFile($metadataPath)) {
            $decoded = json_decode(File::get($metadataPath), true);
            $metadata = is_array($decoded) ? $decoded : [];
        }

        // Rescan files to ensure consistency (Only if metadata is missing to speed up)
        $images = collect($metadata['images'] ?? [])
            ->filter(fn ($image) => is_array($image) && isset($image['filename']))
            ->map(fn ($image) => [
                'filename' => basename($image['filename']),
                'original_name' => $image['original_name'] ?? basename($image['filename']),
                'size' => $image['size'] ?? null,
                'uploaded_at' => $image['uploaded_at'] ?? null,
            ])
            ->values();

        // Only scan disk if there are no images in metadata, to avoid performance issues
        if (empty($metadata)) {
            $knownImages = $images->pluck('filename')->all();

            foreach (File::files($directory) as $file) {
                $extension = strtolower($file->getExtension());
                if (!in_array($extension, self::IMAGE_EXTENSIONS, true)) continue;
                if (in_array($file->getFilename(), $knownImages, true)) continue;

                $images->push([
                    'filename' => $file->getFilename(),
                    'original_name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'uploaded_at' => date('c', $file->getMTime()),
                ]);
            }
        }

        $createdAt = $metadata['created_at'] ?? date('c', File::lastModified($directory));
        $updatedAt = $metadata['updated_at'] ?? $createdAt;

        $displayName = $metadata['folder_name'] ?? Str::headline(str_replace(['_', '-'], ' ', $folder));

        return [
            'folder' => $folder,
            'product_name' => $displayName,
            'folder_name' => $metadata['folder_name'] ?? $displayName,
            'condition' => $metadata['condition'] ?? 'New',
            'folder_type' => $metadata['folder_type'] ?? 'Ebay',
            'location' => $metadata['location'] ?? '',
            'quantity' => (int) ($metadata['quantity'] ?? 1),
            'notes' => $metadata['notes'] ?? '',
            'marked' => $metadata['marked'] ?? false,
            'blinking' => (bool) ($metadata['blinking'] ?? false),
            'user' => $metadata['user'] ?? '',
            'serial_number' => $metadata['serial_number'] ?? '',
            'weight' => $metadata['weight'] ?? '',
            'value' => $metadata['value'] ?? $metadata['estimated_value'] ?? '',
            'estimated_value' => $metadata['value'] ?? $metadata['estimated_value'] ?? '',
            'ebay_link' => $metadata['ebay_link'] ?? '',
            'website_link' => $metadata['website_link'] ?? '',
            'social_media_1' => $metadata['social_media_1'] ?? '',
            'social_media_2' => $metadata['social_media_2'] ?? '',
            'social_media_3' => $metadata['social_media_3'] ?? '',
            'images' => $images->all(),
            'inventory_id' => $metadata['inventory_id'] ?? null,
            'sold'         => (bool) ($metadata['sold'] ?? false),
            'sold_at'      => $metadata['sold_at'] ?? null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'created_at_denver' => $this->formatDenverTimestamp($createdAt),
            'updated_at_denver' => $this->formatDenverTimestamp($updatedAt),
            'path' => $directory,
        ];
    }

    private function statsForProducts(array $products): array
    {
        return [
            'folders'  => count($products),
            'images'   => collect($products)->sum(fn ($product) => count($product['images'])),
            'quantity' => collect($products)->sum(fn ($product) => (int) $product['quantity']),
            'marked'   => collect($products)->filter(fn ($p) => $p['marked'] === true || in_array($p['marked'], ['ebay', 'denver', 'both', 'abid', 'moses'], true))->count(),
            'sold'     => collect($products)->filter(fn ($p) => $p['sold'] === true)->count(),
        ];
    }

    private function fastStats(): array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('ebay_products_stats', function () {
            $basePath = $this->basePath();
            if (!File::isDirectory($basePath)) {
                return ['folders' => 0, 'images' => 0, 'quantity' => 0, 'marked' => 0, 'sold' => 0];
            }

            $folders = 0;
            $images = 0;
            $quantity = 0;
            $marked = 0;
            $sold = 0;

            foreach (File::directories($basePath) as $directory) {
                $folders++;
                $metadataPath = $directory . DIRECTORY_SEPARATOR . 'metadata.json';
                if (File::isFile($metadataPath)) {
                    $decoded = json_decode(File::get($metadataPath), true);
                    if (is_array($decoded)) {
                        $images += count($decoded['images'] ?? []);
                        $quantity += (int) ($decoded['quantity'] ?? 1);
                        $markedVal = $decoded['marked'] ?? false;
                        if ($markedVal === true || in_array($markedVal, ['ebay', 'denver', 'both', 'abid', 'moses'], true)) {
                            $marked++;
                        }
                        if (!empty($decoded['sold'])) {
                            $sold++;
                        }
                    }
                } else {
                    $images += count(File::files($directory));
                    $quantity += 1;
                }
            }

            return [
                'folders'  => $folders,
                'images'   => $images,
                'quantity' => $quantity,
                'marked'   => $marked,
                'sold'     => $sold,
            ];
        });
    }

    private function clearStatsCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('ebay_products_stats');
    }

    private function denverNow(): CarbonImmutable
    {
        return CarbonImmutable::now(self::DENVER_TIMEZONE);
    }

    private function formatDenverTimestamp(?string $timestamp): string
    {
        if (!$timestamp) return '-';
        try {
            return CarbonImmutable::parse($timestamp)
                ->setTimezone(self::DENVER_TIMEZONE)
                ->format('M j, Y g:i A T');
        } catch (\Throwable) {
            return '-';
        }
    }
}
