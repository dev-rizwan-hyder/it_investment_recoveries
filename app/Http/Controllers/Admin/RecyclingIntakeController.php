<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecyclingPalletReceipt;
use App\Models\Client;
use App\Models\Pallet;
use App\Models\Employee;
use App\Models\User;
use Picqer\Barcode\BarcodeGeneratorPNG;

class RecyclingIntakeController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        $pallets_list = Pallet::latest()->limit(50)->get();
        $marketingRefs = ['No reference', 'Facebook', 'Google', 'Referral', 'Trade Show'];

        $employees = Employee::where('is_resigned', 0)->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = User::orderBy('name')->get();
        }

        return view('admin.intake.recycling', compact('clients', 'pallets_list', 'marketingRefs', 'employees'));
    }

    /**
     * Generate a new barcode (AJAX)
     */
    public function getBarcode()
    {
        try {
            $number = 'PAL-' . mt_rand(1000000000, 9999999999);
            $generator = new BarcodeGeneratorPNG();

            $barcodeData = $generator->getBarcode($number, $generator::TYPE_CODE_128, 2, 30);
            $base64 = base64_encode($barcodeData);

            return response()->json([
                'success' => true,
                'number' => $number,
                'base64' => 'data:image/png;base64,' . $base64
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate barcode image for an existing barcode number (AJAX)
     */
    public function getBarcodeImage(Request $request)
    {
        try {
            $barcodeNumber = $request->input('barcode');
            
            if (!$barcodeNumber) {
                return response()->json(['success' => false, 'error' => 'Barcode number is required'], 400);
            }

            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($barcodeNumber, $generator::TYPE_CODE_128, 2, 30);
            $base64 = base64_encode($barcodeData);

            return response()->json([
                'success' => true,
                'number' => $barcodeNumber,
                'base64' => 'data:image/png;base64,' . $base64
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate a new numeric job number (AJAX)
     */
    public function getJobNumber()
    {
        try {
            do {
                $jobNo = (string) mt_rand(100000, 999999);
            } while (Pallet::where('job_no', $jobNo)->exists());

            return response()->json([
                'success' => true,
                'job_no' => $jobNo
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get clients list (AJAX)
     */
    public function getClientsList()
    {
        try {
            $clients = Client::orderBy('name')->get(['id', 'name', 'company', 'email']);
            return response()->json([
                'success' => true,
                'clients' => $clients
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getPalletDetails($id)
    {
        try {
            // Crucial: with('client') must be here!
            $pallet = Pallet::with('client')->find($id);

            if (!$pallet) {
                return response()->json(['success' => false, 'message' => 'Pallet not found'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pallet
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Calculate net weight (AJAX)
     */
    public function calculateNet(Request $request)
    {
        try {
            $gross = (float) ($request->grossWeight ?? 0);
            $tare = (float) ($request->tareWeight ?? 0);
            $net = max(0, $gross - $tare);

            return response()->json([
                'success' => true,
                'netWeight' => round($net, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new client (AJAX)
     */
    public function createClient(Request $request)
    {
        try {
            $validated = $request->validate([
                'clientName' => 'required|string|min:2',
                'clientEmail' => 'nullable|email',
                'clientPhone' => 'nullable|string',
                'clientCompany' => 'nullable|string',
                'clientAddress' => 'nullable|string',
            ]);

            $client = Client::create([
                'name' => $validated['clientName'],
                'email' => $validated['clientEmail'],
                'phone' => $validated['clientPhone'],
                'company' => $validated['clientCompany'],
                'address' => $validated['clientAddress'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Save photos as Base64 data URIs (stored directly in DB, no disk files)
     */
    private function savePalletPhotos($barcode, $photos)
    {
        $photoPaths = [];
        
        if (empty($photos)) {
            return $photoPaths;
        }

        // Flatten any nested arrays/structures of UploadedFile objects
        $flattened = [];
        $arrayIter = is_array($photos) ? $photos : [$photos];
        array_walk_recursive($arrayIter, function ($item) use (&$flattened) {
            if ($item instanceof \Illuminate\Http\UploadedFile) {
                $flattened[] = $item;
            }
        });

        if (empty($flattened)) {
            return $photoPaths;
        }

        foreach ($flattened as $photo) {
            if (!$photo->isValid()) {
                continue;
            }

            try {
                $photoPaths[] = $this->convertPhotoToBase64($photo);
            } catch (\Throwable $e) {
                \Log::error('Recycling photo base64 conversion failed:', ['barcode' => $barcode, 'error' => $e->getMessage()]);
            }
        }

        return array_values(array_unique($photoPaths));
    }

    /**
     * Convert an uploaded photo into an optimized Base64 data URI string.
     */
    private function convertPhotoToBase64(\Illuminate\Http\UploadedFile $photo): string
    {
        $realPath = $photo->getRealPath();
        $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType() ?: 'image/jpeg';

        try {
            if (function_exists('imagecreatefromstring') && function_exists('imagejpeg') && str_starts_with($mimeType, 'image/')) {
                $rawContent = file_get_contents($realPath);
                $img = @imagecreatefromstring($rawContent);

                if ($img !== false) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $maxDimension = 1600;

                    if ($width > $maxDimension || $height > $maxDimension) {
                        if ($width >= $height) {
                            $newWidth = $maxDimension;
                            $newHeight = (int) round(($height / $width) * $maxDimension);
                        } else {
                            $newHeight = $maxDimension;
                            $newWidth = (int) round(($width / $height) * $maxDimension);
                        }

                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($img);
                        $img = $resized;
                    }

                    ob_start();
                    if ($mimeType === 'image/png') {
                        imagepng($img, null, 6);
                    } elseif ($mimeType === 'image/webp') {
                        imagewebp($img, null, 80);
                    } else {
                        imagejpeg($img, null, 82);
                        $mimeType = 'image/jpeg';
                    }
                    $compressedData = ob_get_clean();
                    imagedestroy($img);

                    if (!empty($compressedData)) {
                        return 'data:' . $mimeType . ';base64,' . base64_encode($compressedData);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Photo optimization failed, falling back to raw base64: ' . $e->getMessage());
        }

        $base64Data = base64_encode(file_get_contents($realPath));
        return 'data:' . $mimeType . ';base64,' . $base64Data;
    }

    private function sendPalletReceiptEmail($pallets): array
    {
        $pallets = collect($pallets)->filter();

        if ($pallets->isEmpty()) {
            return [
                'sent' => false,
                'message' => 'No pallet details were available to email.',
            ];
        }

        $client = $pallets->first()->client;

        if (!$client || empty($client->email)) {
            return [
                'sent' => false,
                'message' => 'Client email address is missing.',
            ];
        }

        try {
            Mail::to($client->email)->send(new RecyclingPalletReceipt($pallets));

            return [
                'sent' => true,
                'message' => 'Receipt emailed to ' . $client->email . '.',
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to send recycling pallet receipt email.', [
                'client_id' => $client->id,
                'email' => $client->email,
                'barcode' => $pallets->first()->barcode_number ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'sent' => false,
                'message' => 'Pallet saved, but the receipt email could not be sent.',
            ];
        }
    }

    /**
     * Save quick intake (AJAX)
     */
    public function saveQuick(Request $request)
    {
        try {
            $validated = $request->validate([
                'barcodeNumber' => 'required|string',
                'selectedVendor' => 'required|exists:clients,id',
                'intakeDate' => 'nullable|date',
                'putAwayLocation' => 'required|string',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
                'receivingEmployee' => 'nullable|string',
                'processingEmployee' => 'nullable|string',
                'jobNo' => 'nullable|string',
                'job_no' => 'nullable|string',
                'sendEmail' => 'nullable|boolean',
                'main_photos' => 'nullable',
                'main_photos.*' => 'nullable',
                'status' => 'nullable|string',
            ]);

            $intakeDateRaw = $validated['intakeDate'] ?? $request->input('intakeDate') ?? $request->input('intake_date') ?? $request->input('intakeDateStep1') ?? $request->input('intakeDateQuick');
            $palletDate = !empty($intakeDateRaw)
                ? \Carbon\Carbon::parse($intakeDateRaw)->setTimeFrom(now())
                : now();

            $status = (strtolower($request->input('status', 'Received')) === 'draft') ? 'Draft' : 'Received';

            // Handle photos - save to public/pallets/{barcode} folder
            $photoPaths = [];
            $photos = $request->file('main_photos') ?: $request->file('photos');
            if (!empty($photos)) {
                $photoPaths = $this->savePalletPhotos(
                    $validated['barcodeNumber'],
                    $photos
                );
            }

            $pallet = Pallet::create([
                'barcode_number' => $validated['barcodeNumber'],
                'client_id' => $validated['selectedVendor'],
                'description' => $validated['description'] ?? '',
                'put_away_location' => $validated['putAwayLocation'],
                'notes' => $validated['notes'] ?? '',
                'receiving_employee' => $validated['receivingEmployee'] ?? null,
                'job_no' => $validated['jobNo'] ?? ($request->jobNo ?? ($request->job_no ?? null)),
                'processing_employee' => $validated['processingEmployee'] ?? ($request->processingEmployee ?? null),
                'marketing_reference' => 'No reference',
                'intake_mode' => 'quick',
                'status' => $status,
                'gross_weight' => 0,
                'tare_weight' => 0,
                'net_weight' => 0,
                'estimated_count' => 0,
                'photos_paths' => $photoPaths,
                'created_at' => $palletDate,
                'updated_at' => now(),
            ]);

            $emailStatus = null;
            if ($status !== 'Draft' && ($validated['sendEmail'] ?? false)) {
                $emailStatus = $this->sendPalletReceiptEmail([$pallet->load('client')]);
            }

            $message = ($status === 'Draft') ? 'Quick intake saved as draft successfully' : 'Quick intake saved successfully';
            if ($emailStatus) {
                $message .= '. ' . $emailStatus['message'];
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'barcode' => $pallet->barcode_number,
                'emailSent' => $emailStatus['sent'] ?? false,
                'pallet' => $pallet
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Save full intake with multiple items (AJAX)
     */
    public function saveFull(Request $request)
    {
        try {
            // Log the entire request
            \Log::info('saveFull called', [
                'method' => $request->getMethod(),
                'all_data_keys' => array_keys($request->all()),
                'all_files_keys' => array_keys($request->allFiles()),
                'content_type' => $request->header('Content-Type'),
            ]);
            
            $validated = $request->validate([
                'barcodeNumber' => 'required|string',
                'selectedVendor' => 'required|exists:clients,id',
                'intakeDate' => 'nullable|date',
                'putAwayLocation' => 'required|string',
                'description' => 'nullable|string',
                'grossWeight' => 'nullable|numeric|min:0',
                'tareWeight' => 'nullable|numeric|min:0',
                'netWeight' => 'nullable|numeric|min:0',
                'estimatedCount' => 'nullable|integer|min:0',
                'marketingReference' => 'nullable|string',
                'notes' => 'nullable|string',
                'receivingEmployee' => 'nullable|string',
                'processingEmployee' => 'nullable|string',
                'jobNo' => 'nullable|string',
                'job_no' => 'nullable|string',
                'main_photos' => 'nullable',
                'main_photos.*' => 'nullable',
                'palletItems' => 'nullable|array',
                'palletItems.*.description' => 'nullable|string',
                'palletItems.*.estimatedCount' => 'nullable|integer|min:0',
                'palletItems.*.grossWeight' => 'nullable|numeric|min:0',
                'palletItems.*.tareWeight' => 'nullable|numeric|min:0',
                'palletItems.*.netWeight' => 'nullable|numeric|min:0',
                'palletItems.*.putAwayLocation' => 'required|string',
                'palletItems.*.marketingReference' => 'nullable|string',
                'palletItems.*.notes' => 'nullable|string',
                'palletItems.*.receivingEmployee' => 'nullable|string',
                'palletItems.*.processingEmployee' => 'nullable|string',
                'palletItems.*.jobNo' => 'nullable|string',
                'palletItems.*.job_no' => 'nullable|string',
                'palletItems.*.photos' => 'nullable',
                'palletItems.*.photos.*' => 'nullable',
                'sendEmail' => 'nullable|boolean',
                'status' => 'nullable|string',
            ]);

            $intakeDateRaw = $validated['intakeDate'] ?? $request->input('intakeDate') ?? $request->input('intake_date') ?? $request->input('intakeDateStep1') ?? $request->input('intakeDateQuick');
            $palletDate = !empty($intakeDateRaw)
                ? \Carbon\Carbon::parse($intakeDateRaw)->setTimeFrom(now())
                : now();

            $targetStatus = (strtolower($request->input('status', 'Received')) === 'draft') ? 'Draft' : 'Received';

            $savedCount = 0;
            $savedPalletIds = [];
            $barcodeNumber = $validated['barcodeNumber'];

            DB::transaction(function () use ($request, $validated, &$savedCount, &$savedPalletIds, $barcodeNumber, $palletDate, $targetStatus) {

                // 1. Handle Main Pallet Photos
                $mainPhotoPaths = [];
                $mainPhotos = $request->file('main_photos') ?: $request->file('photos');
                if (!empty($mainPhotos)) {
                    $mainPhotoPaths = $this->savePalletPhotos(
                        $barcodeNumber,
                        $mainPhotos
                    );
                }
                
                \Log::debug('saveFull - Final main_photos array:', [
                    'paths' => $mainPhotoPaths,
                    'count' => count($mainPhotoPaths),
                ]);

                // FIX: Main Item mein estimated_count add kiya
                $mainPallet = Pallet::create([
                    'barcode_number' => $validated['barcodeNumber'],
                    'client_id' => $validated['selectedVendor'],
                    'description' => $validated['description'] ?? '',
                    'gross_weight' => $validated['grossWeight'],
                    'tare_weight' => $validated['tareWeight'] ?? 0,
                    'net_weight' => $validated['netWeight'] ?? 0,
                    'put_away_location' => $validated['putAwayLocation'],
                    'estimated_count' => $validated['estimatedCount'] ?? 0, // YEH LINE ADD KAREIN
                    'intake_mode' => 'full',
                    'status' => $targetStatus,
                    'marketing_reference' => $validated['marketingReference'] ?? 'No reference',
                    'photos_paths' => $mainPhotoPaths,
                    'notes' => $validated['notes'] ?? '',
                    'receiving_employee' => $validated['receivingEmployee'] ?? null,
                    'job_no' => $validated['jobNo'] ?? ($request->jobNo ?? ($request->job_no ?? null)),
                    'processing_employee' => $validated['processingEmployee'] ?? ($request->processingEmployee ?? null),
                    'created_at' => $palletDate,
                    'updated_at' => now(),
                ]);
                $savedPalletIds[] = $mainPallet->id;
                $savedCount++;

                // 2. Additional Items loop
                if (!empty($validated['palletItems'])) {
                    foreach ($validated['palletItems'] as $index => $item) {

                        $itemPhotoPaths = [];
                        if ($request->hasFile("palletItems.$index.photos")) {
                            $itemPhotoPaths = $this->savePalletPhotos(
                                $barcodeNumber,
                                $request->file("palletItems.$index.photos")
                            );
                        }

                        // FIX: Additional Items mein bhi estimated_count add kiya
                        $additionalPallet = Pallet::create([
                            'barcode_number' => $validated['barcodeNumber'],
                            'client_id' => $validated['selectedVendor'],
                            'description' => $item['description'] ?? '',
                            'gross_weight' => $item['grossWeight'],
                            'tare_weight' => $item['tareWeight'] ?? 0,
                            'net_weight' => $item['netWeight'],
                            'put_away_location' => $item['putAwayLocation'],
                            'estimated_count' => $item['estimatedCount'] ?? 0, // YEH LINE BHI ADD KAREIN
                            'marketing_reference' => $item['marketingReference'] ?? 'No reference',
                            'intake_mode' => 'full',
                            'status' => $targetStatus,
                            'photos_paths' => $itemPhotoPaths,
                            'notes' => $item['notes'] ?? '',
                            'receiving_employee' => $item['receivingEmployee'] ?? ($validated['receivingEmployee'] ?? null),
                            'job_no' => $item['jobNo'] ?? ($item['job_no'] ?? ($validated['jobNo'] ?? ($request->jobNo ?? ($request->job_no ?? null)))),
                            'processing_employee' => $item['processingEmployee'] ?? ($validated['processingEmployee'] ?? ($request->processingEmployee ?? null)),
                            'created_at' => $palletDate,
                            'updated_at' => now(),
                        ]);
                        $savedPalletIds[] = $additionalPallet->id;
                        $savedCount++;
                    }
                }
            });

            $savedPallets = Pallet::with('client')
                ->whereIn('id', $savedPalletIds)
                ->get()
                ->sortBy(fn ($pallet) => array_search($pallet->id, $savedPalletIds, true))
                ->values();

            $isDraft = $targetStatus === 'Draft';
            $emailStatus = null;
            if (!$isDraft && ($validated['sendEmail'] ?? false)) {
                $emailStatus = $this->sendPalletReceiptEmail($savedPallets);
            }

            $message = $isDraft 
                ? "Full intake saved as draft successfully ($savedCount items)"
                : "Full intake saved successfully ($savedCount items)";
            if ($emailStatus) {
                $message .= '. ' . $emailStatus['message'];
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'barcode' => $validated['barcodeNumber'],
                'emailSent' => $emailStatus['sent'] ?? false,
                'itemCount' => $savedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Save Full Intake Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
