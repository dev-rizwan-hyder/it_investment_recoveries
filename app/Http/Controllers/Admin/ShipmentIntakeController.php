<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ShipmentIntake;
use App\Models\UniversalWasteItem;
use App\Models\User;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ShipmentIntakeController extends Controller
{
    // ----------------------------------------------------------------
    //  HELPER: photo → base64
    // ----------------------------------------------------------------
    private function convertPhotoToBase64(\Illuminate\Http\UploadedFile $photo): string
    {
        $realPath = $photo->getRealPath();
        $mimeType = $photo->getMimeType() ?: $photo->getClientMimeType() ?: 'image/jpeg';

        try {
            if (function_exists('imagecreatefromstring') && str_starts_with($mimeType, 'image/')) {
                $rawContent = file_get_contents($realPath);
                $img        = @imagecreatefromstring($rawContent);
                if ($img !== false) {
                    $w = imagesx($img); $h = imagesy($img); $max = 1600;
                    if ($w > $max || $h > $max) {
                        if ($w >= $h) { $nw = $max; $nh = (int) round(($h / $w) * $max); }
                        else          { $nh = $max; $nw = (int) round(($w / $h) * $max); }
                        $r = imagecreatetruecolor($nw, $nh);
                        imagealphablending($r, false); imagesavealpha($r, true);
                        imagecopyresampled($r, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
                        imagedestroy($img); $img = $r;
                    }
                    ob_start();
                    if ($mimeType === 'image/png') imagepng($img, null, 6);
                    elseif ($mimeType === 'image/webp') imagewebp($img, null, 80);
                    else { imagejpeg($img, null, 82); $mimeType = 'image/jpeg'; }
                    $data = ob_get_clean(); imagedestroy($img);
                    if (!empty($data)) return 'data:' . $mimeType . ';base64,' . base64_encode($data);
                }
            }
        } catch (\Throwable) {}

        return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($realPath));
    }

    private function savePhotos(array $photos): array
    {
        $paths = [];
        foreach ($photos as $photo) {
            if ($photo instanceof \Illuminate\Http\UploadedFile && $photo->isValid()) {
                try { $paths[] = $this->convertPhotoToBase64($photo); } catch (\Throwable) {}
            }
        }
        return array_values(array_unique($paths));
    }

    // ----------------------------------------------------------------
    //  INDEX – form page
    // ----------------------------------------------------------------
    public function index()
    {
        $clients       = Client::orderBy('name')->get();
        $employees     = Employee::where('is_resigned', 0)->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = User::orderBy('name')->get();
        }
        $marketingRefs = ['No reference', 'Facebook', 'Google', 'Referral', 'Trade Show'];

        // Only completed universal waste entries that have a pallet_number (barcode)
        // Exclude those already used in a shipment intake
        $usedIds = ShipmentIntake::whereNotNull('universal_waste_item_id')
            ->pluck('universal_waste_item_id')->toArray();
        $uwEntries = UniversalWasteItem::where('status', 'Completed')
            ->whereNotNull('pallet_number')
            ->whereNotIn('id', $usedIds)
            ->with(['pallet.client'])
            ->orderBy('pallet_number')
            ->get();

        return view('admin.intake.shipment-intake', compact('clients', 'employees', 'marketingRefs', 'uwEntries'));
    }

    // ----------------------------------------------------------------
    //  AJAX – get barcode
    // ----------------------------------------------------------------
    public function getBarcode()
    {
        try {
            $number    = 'SHP-' . mt_rand(1000000000, 9999999999);
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($number, $generator::TYPE_CODE_128, 2, 30);
            return response()->json(['success' => true, 'number' => $number,
                'base64' => 'data:image/png;base64,' . base64_encode($barcodeData)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ----------------------------------------------------------------
    //  AJAX – get job number
    // ----------------------------------------------------------------
    public function getJobNumber()
    {
        try {
            do { $jobNo = (string) mt_rand(100000, 999999); }
            while (ShipmentIntake::where('job_no', $jobNo)->exists());
            return response()->json(['success' => true, 'job_no' => $jobNo]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ----------------------------------------------------------------
    //  AJAX – clients list
    // ----------------------------------------------------------------
    public function getClientsList()
    {
        $clients = Client::orderBy('name')->get(['id', 'name', 'company', 'email']);
        return response()->json(['success' => true, 'clients' => $clients]);
    }

    // ----------------------------------------------------------------
    //  AJAX – create client
    // ----------------------------------------------------------------
    public function createClient(Request $request)
    {
        try {
            $v = $request->validate([
                'clientName'    => 'required|string|min:2',
                'clientEmail'   => 'nullable|email',
                'clientPhone'   => 'nullable|string',
                'clientCompany' => 'nullable|string',
                'clientAddress' => 'nullable|string',
            ]);
            $client = Client::create([
                'name'    => $v['clientName'],
                'email'   => $v['clientEmail'],
                'phone'   => $v['clientPhone'],
                'company' => $v['clientCompany'],
                'address' => $v['clientAddress'],
            ]);
            return response()->json(['success' => true, 'client' => $client]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ----------------------------------------------------------------
    //  AJAX – get UW entry details (barcode image + client info)
    // ----------------------------------------------------------------
    public function getUwEntryDetails(Request $request)
    {
        try {
            $id   = $request->input('id');
            $item = UniversalWasteItem::with(['pallet.client'])->where('status', 'Completed')->findOrFail($id);
            $generator = new BarcodeGeneratorPNG();
            $barcodeNum = $item->pallet_number;
            $barcodeData = $generator->getBarcode($barcodeNum, $generator::TYPE_CODE_128, 2, 30);
            return response()->json([
                'success' => true,
                'item'    => [
                    'id'            => $item->id,
                    'pallet_number' => $barcodeNum,
                    'name'          => $item->name,
                    'brand'         => $item->brand,
                    'model'         => $item->model,
                    'category'      => $item->category,
                    'quantity'      => $item->quantity,
                    'weight'        => $item->weight,
                    'notes'         => $item->notes,
                    'client_id'     => $item->pallet?->client_id,
                    'client_name'   => $item->pallet?->client?->name,
                    'client_company'=> $item->pallet?->client?->company,
                    'client_email'  => $item->pallet?->client?->email,
                ],
                'barcode_base64' => 'data:image/png;base64,' . base64_encode($barcodeData),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ----------------------------------------------------------------
    //  SAVE – create a shipment intake
    // ----------------------------------------------------------------
    public function save(Request $request)
    {
        try {
            $v = $request->validate([
                'intake_date'              => 'nullable|date',
                'client_id'                => 'required|exists:clients,id',
                'job_no'                   => 'nullable|string',
                'barcode_number'           => 'nullable|string',
                'universal_waste_item_id'  => 'nullable|integer|exists:universal_waste_items,id',
                'pallet_number'            => 'nullable|string',
                'description'              => 'nullable|string',
                'estimated_count'          => 'nullable|integer|min:0',
                'gross_weight'             => 'nullable|numeric|min:0',
                'tare_weight'              => 'nullable|numeric|min:0',
                'net_weight'               => 'nullable|numeric|min:0',
                'put_away_location'        => 'required|string',
                'marketing_reference'      => 'nullable|string',
                'receiving_employee'       => 'nullable|string',
                'notes'                    => 'nullable|string',
                'main_photos'              => 'nullable',
                'main_photos.*'            => 'nullable',
            ]);

            $photoPaths = [];
            $photos = $request->file('main_photos') ?: [];
            if (!empty($photos)) {
                $flat = [];
                array_walk_recursive(is_array($photos) ? $photos : [$photos], function ($item) use (&$flat) {
                    if ($item instanceof \Illuminate\Http\UploadedFile) $flat[] = $item;
                });
                $photoPaths = $this->savePhotos($flat);
            }

            $intakeDate = !empty($v['intake_date'])
                ? \Carbon\Carbon::parse($v['intake_date'])->setTimeFrom(now())
                : now();

            $shipment = ShipmentIntake::create([
                'intake_date'             => $intakeDate,
                'client_id'               => $v['client_id'],
                'job_no'                  => $v['job_no'] ?? null,
                'barcode_number'          => $v['barcode_number'] ?? null,
                'universal_waste_item_id' => $v['universal_waste_item_id'] ?? null,
                'pallet_number'           => $v['pallet_number'] ?? null,
                'description'             => $v['description'] ?? '',
                'estimated_count'         => $v['estimated_count'] ?? 0,
                'gross_weight'            => $v['gross_weight'] ?? 0,
                'tare_weight'             => $v['tare_weight'] ?? 0,
                'net_weight'              => $v['net_weight'] ?? 0,
                'put_away_location'       => $v['put_away_location'],
                'marketing_reference'     => $v['marketing_reference'] ?? 'No reference',
                'receiving_employee'      => $v['receiving_employee'] ?? null,
                'notes'                   => $v['notes'] ?? '',
                'photos_paths'            => $photoPaths,
                'status'                  => 'Received',
                'created_at'              => $intakeDate,
                'updated_at'              => now(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Shipment intake saved successfully!',
                'shipment' => $shipment,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ================================================================
    //  DOWNSTREAM VENDOR – listing
    // ================================================================
    public function downstreamIndex()
    {
        $clients = \App\Models\Client::orderBy('name')->get();
        return view('admin.intake.downstream-vendor', compact('clients'));
    }

    public function downstreamLoadData(Request $request)
    {
        $search   = $request->get('search', '');
        $status   = $request->get('statusFilter', '');
        $clientId = $request->get('clientFilter', '');
        $page     = $request->get('page', 1);
        $perPage  = 10;

        $query = ShipmentIntake::with('client');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('barcode_number', 'like', "%{$search}%")
                  ->orWhere('pallet_number', 'like', "%{$search}%")
                  ->orWhere('job_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%")
                                                        ->orWhere('company', 'like', "%{$search}%"));
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($clientId)) {
            $query->where('client_id', $clientId);
        }

        $items = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        $items->getCollection()->transform(function ($item) {
            $item->client_name   = $item->client?->name    ?? 'N/A';
            $item->company_name  = $item->client?->company ?? 'N/A';
            $item->photos_count  = is_array($item->photos_paths) ? count($item->photos_paths) : 0;
            return $item;
        });

        return response()->json([
            'items'        => $items->items(),
            'total'        => $items->total(),
            'per_page'     => $items->perPage(),
            'current_page' => $items->currentPage(),
            'last_page'    => $items->lastPage(),
        ]);
    }

    public function downstreamStats()
    {
        return response()->json([
            ['label' => 'Total',     'value' => ShipmentIntake::count(),                       'color' => 'text-blue-600'],
            ['label' => 'Received',  'value' => ShipmentIntake::where('status','Received')->count(), 'color' => 'text-emerald-600'],
            ['label' => 'Processing','value' => ShipmentIntake::where('status','Processing')->count(),'color' => 'text-orange-500'],
            ['label' => 'Completed', 'value' => ShipmentIntake::where('status','Completed')->count(), 'color' => 'text-purple-600'],
        ]);
    }

    public function downstreamDelete($id)
    {
        try {
            ShipmentIntake::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function downstreamBulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            ShipmentIntake::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' records deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function downstreamUpdateStatus(Request $request, $id)
    {
        try {
            $shipment = ShipmentIntake::findOrFail($id);
            $shipment->status = $request->input('status', $shipment->status);
            $shipment->save();
            return response()->json(['success' => true, 'message' => 'Status updated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function downstreamExportCsv()
    {
        $items = ShipmentIntake::with('client')->latest()->get();
        $csv = "ID,Date,Client,Company,Job #,Barcode,Pallet #,Description,Count,Gross,Tare,Net,Location,Employee,Status\n";
        foreach ($items as $item) {
            $csv .= implode(',', [
                $item->id,
                $item->intake_date?->format('Y-m-d') ?? '',
                '"' . addslashes($item->client?->name    ?? '') . '"',
                '"' . addslashes($item->client?->company ?? '') . '"',
                $item->job_no,
                $item->barcode_number,
                $item->pallet_number,
                '"' . addslashes($item->description ?? '') . '"',
                $item->estimated_count,
                $item->gross_weight,
                $item->tare_weight,
                $item->net_weight,
                '"' . addslashes($item->put_away_location ?? '') . '"',
                '"' . addslashes($item->receiving_employee ?? '') . '"',
                $item->status,
            ]) . "\n";
        }
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="downstream-vendor-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
