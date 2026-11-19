<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    // Standard Emission Factors
    protected $EMISSION_FACTORS = [
        'laptop' => 4.5,
        'desktop' => 5.2,
        'monitor' => 3.8,
        'server' => 8.0,
        'default' => 2.5
    ];

    public function index()
    {
        $clients = Client::orderBy('name')->get();
        return view('admin.report.index', compact('clients'));
    }

    public function preview()
    {
        $report = session('admin.report.preview');

        if (!$report || empty($report['items'])) {
            return redirect()->route('admin.report.index')
                ->with('error', 'Please generate a report first.');
        }

        $items = collect($report['items'])->map(function ($item) {
            return (object) $item;
        });
        $results = $report['results'];
        $type = $report['type'] ?? 'All';

        return view('admin.report.preview', compact('items', 'results', 'type'));
    }

    public function fetchItems(Request $request)
    {
        $clientId = $request->client_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$clientId) {
            return response()->json([]);
        }

        $client = DB::table('clients')->whereNull('deleted_at')->find($clientId);
        if (!$client) {
            return response()->json([]);
        }

        $palletQuery = DB::table('pallets')
            ->where('client_id', (int) $clientId)
            ->whereNull('deleted_at');

        if (!empty($startDate) && !empty($endDate)) {
            $palletQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif (!empty($startDate)) {
            $palletQuery->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif (!empty($endDate)) {
            $palletQuery->where('created_at', '<=', $endDate . ' 23:59:59');
        }

        $palletsList = $palletQuery->orderBy('created_at', 'desc')->get();
        $combinedItems = collect();

        foreach ($palletsList as $p) {
            $combinedItems->push((object)[
                'unique_key' => 'pallets_' . $p->id,
                'table_name' => 'pallets',
                'raw_id' => $p->id,
                'name' => $p->description ?: ('Processing Pallet #' . $p->barcode_number),
                'brand' => $p->barcode_number,
                'model' => $p->put_away_location ?: 'N/A',
                'serial_number' => $p->marketing_reference ?: 'N/A',
                'category' => 'Processing Intake',
                'condition' => $p->status ?: 'Received',
                'quantity' => (int) ($p->estimated_count ?? 1),
                'weight' => (float) ($p->net_weight ?? $p->gross_weight ?? 0),
                'source_type' => 'Processing Intake',
                'created_at' => $p->created_at,
            ]);
        }

        return response()->json($combinedItems->values()->toArray());
    }

    public function store(Request $request)
    {
        $selectedKeys = $request->input('selected_items', []);
        $clientId = $request->input('client_id_hidden');

        if (empty($selectedKeys)) {
            return back()->with('error', 'Please select processing entries to generate report.');
        }

        $client = DB::table('clients')->whereNull('deleted_at')->find($clientId);
        if (!$client) {
            return back()->with('error', 'Client information not found.');
        }

        $items = collect();

        foreach ($selectedKeys as $key) {
            if (strpos($key, '_') !== false) {
                $lastUnderscore = strrpos($key, '_');
                $rawId = (int) substr($key, $lastUnderscore + 1);
            } else {
                $rawId = (int) $key;
            }

            $p = DB::table('pallets')->whereNull('deleted_at')->find($rawId);
            if ($p) {
                $items->push((object)[
                    'id' => $p->id,
                    'name' => $p->description ?: ('Processing Pallet #' . $p->barcode_number),
                    'brand' => $p->barcode_number,
                    'model' => $p->put_away_location ?: 'N/A',
                    'serial_number' => $p->marketing_reference ?: 'N/A',
                    'category' => 'Processing Intake',
                    'condition' => $p->status ?: 'Received',
                    'quantity' => (int) ($p->estimated_count ?? 1),
                    'weight' => (float) ($p->net_weight ?? $p->gross_weight ?? 0),
                    'client_display_name' => $client->name,
                    'company' => $client->company,
                    'address' => $client->address,
                    'email' => $client->email,
                    'phone' => $client->phone,
                ]);
            }
        }

        if ($items->isEmpty()) {
            return back()->with('error', 'No selected processing entries could be found.');
        }

        $totalUnits = $items->sum('quantity');
        $totalWeightLbs = (float) $items->sum('weight');
        $co2AvoidedLbs = 0;

        foreach ($items as $item) {
            $catKey = strtolower($item->category ?? '');
            $factor = $this->EMISSION_FACTORS[$catKey] ?? $this->EMISSION_FACTORS['default'];
            $co2AvoidedLbs += ((float) ($item->weight ?? 0) * $factor);
        }

        $results = [
            'totalUnits' => $totalUnits,
            'totalWeightLbs' => $totalWeightLbs,
            'co2AvoidedLbs' => $co2AvoidedLbs,
            'treesEquivalent' => round($co2AvoidedLbs / 48),
            'carsOffRoadDays' => round($co2AvoidedLbs / 24.6),
            'homesEnergyDays' => round($co2AvoidedLbs / 30),
            'waterSavedGallons' => round($totalWeightLbs * 0.5),
        ];

        $items = $items->values();

        session()->put('admin.report.preview', [
            'items' => $items->toArray(),
            'results' => $results,
            'type' => 'Processing Intake',
            'client_email' => $client->email ?? null,
            'client_name' => $client->name ?? null,
        ]);

        return redirect()->route('admin.report.preview', [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);
    }

    public function sendEmail(Request $request)
    {
        $report = session('admin.report.preview');

        if (!$report || empty($report['items'])) {
            return back()->with('error', 'Please generate the report again before sending it by email.');
        }

        $email = $request->input('client_email') ?: ($report['client_email'] ?? null);

        if (!$email) {
            return back()->with('error', 'Client email address is missing for this report.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Client email address is invalid.');
        }

        $defaultMailer = config('mail.default');
        if (in_array($defaultMailer, ['log', 'array'], true)) {
            return back()->with(
                'error',
                'Email delivery is not configured. Current mailer is "' . $defaultMailer . '". Update MAIL_MAILER and SMTP settings in .env to send real emails.'
            );
        }

        $items = collect($report['items'])->map(function ($item) {
            return (object) $item;
        });
        $results = $report['results'];
        $type = $report['type'] ?? 'All';

        $pdf = Pdf::loadView('admin.report.pdf', [
            'items' => $items,
            'results' => $results,
            'type' => $type,
        ]);

        $clientName = $report['client_name'] ?? ($items->first()->client_display_name ?? 'Client');

        try {
            Mail::html(
                '<p>Hello,</p><p>Your ESG Impact Report is attached to this email as a PDF.</p><p>Regards,<br>IT Investment Portal</p>',
                function ($message) use ($email, $clientName, $pdf) {
                    $message->to($email)
                        ->subject('ESG Impact Report - ' . $clientName)
                        ->attachData($pdf->output(), 'esg-impact-report.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                }
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send ESG report email.', [
                'email' => $email,
                'client_name' => $clientName,
                'mailer' => $defaultMailer,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send email. Please verify SMTP settings and try again.');
        }

        return back()->with('success', 'Report emailed successfully to ' . $email . '.');
    }

    public function debugData(Request $request)
    {
        $clientId = $request->client_id;

        if (!$clientId) {
            return response()->json(['error' => 'No client ID provided']);
        }

        return response()->json([
            'client_id' => $clientId,
            'pallets' => DB::table('pallets')
                ->where('client_id', $clientId)
                ->select('id', 'barcode_number', 'created_at')
                ->get(),
            'ecommerce_items' => DB::table('ecommerce_items')
                ->select('id', 'pallet_number', 'status', 'created_at', 'category', 'quantity', 'weight')
                ->limit(5)
                ->get(),
            'refurbishing_items' => DB::table('refurbishing_items')
                ->select('id', 'pallet_number', 'status', 'created_at', 'category', 'quantity', 'weight')
                ->limit(5)
                ->get(),
            'universal_waste_items' => DB::table('universal_waste_items')
                ->select('id', 'pallet_number', 'status', 'created_at', 'category', 'quantity', 'weight')
                ->limit(5)
                ->get(),
            'data_destruction_items' => DB::table('data_destruction_items')
                ->select('id', 'pallet_number', 'status', 'created_at', 'category', 'quantity', 'weight')
                ->limit(5)
                ->get(),
        ]);
    }
}