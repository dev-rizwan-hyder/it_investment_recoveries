<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecyclingPalletReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $pallets;
    public $client;
    public array $totals;
    public string $receiptNumber;
    public ?string $jobNo = null;

    public string $portalUrl;
    public string $trackingUrl;
    public string $loginUrl;
    public string $registerUrl;

    public function __construct($pallets)
    {
        $this->pallets = collect($pallets)->values();
        $this->client = $this->pallets->first()?->client;

        $firstJob = $this->pallets->pluck('job_no')->filter()->first() ?? $this->pallets->first()?->job_no;
        $this->jobNo = $firstJob ? trim((string) $firstJob) : null;

        $this->receiptNumber = !empty($this->jobNo)
            ? (preg_match('/^job/i', $this->jobNo) ? $this->jobNo : 'Job #: ' . $this->jobNo)
            : ('REC-' . now('America/Denver')->format('YmdHis'));

        $this->totals = [
            'items' => $this->pallets->count(),
            'estimated_count' => (int) $this->pallets->sum(fn ($pallet) => (int) ($pallet->estimated_count ?? 0)),
            'gross_weight' => (float) $this->pallets->sum(fn ($pallet) => (float) ($pallet->gross_weight ?? 0)),
            'tare_weight' => (float) $this->pallets->sum(fn ($pallet) => (float) ($pallet->tare_weight ?? 0)),
            'net_weight' => (float) $this->pallets->sum(fn ($pallet) => (float) ($pallet->net_weight ?? 0)),
        ];

        $baseUrl = rtrim(env('IT_RECOVERIES_URL', 'https://it-investmentrecoveries.com'), '/');
        $this->portalUrl = $baseUrl;
        $this->loginUrl = $baseUrl . '/login';
        $this->registerUrl = $baseUrl . '/register';
        $this->trackingUrl = $baseUrl . '/register';
    }

    public function build()
    {
        $barcode = $this->pallets->first()?->barcode_number ?? 'Pallet';
        $subjectRef = !empty($this->jobNo)
            ? (preg_match('/^job/i', $this->jobNo) ? $this->jobNo : 'Job #' . $this->jobNo)
            : $barcode;

        return $this->from(
            config('mail.from.address', 'customerservice@it-investmentrecoveries.com'),
            config('mail.from.name', 'IT Investment Recoveries')
        )
            ->subject('Recycling Intake Receipt - ' . $subjectRef)
            ->view('emails.recycling_pallet_receipt');
    }
}
