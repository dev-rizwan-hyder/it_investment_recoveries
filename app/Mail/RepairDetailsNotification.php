<?php

namespace App\Mail;

use App\Models\Pallet;
use App\Models\RepairEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class RepairDetailsNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $repairs;
    public ?RepairEntry $primaryRepair;
    public ?Pallet $pallet;
    public $client;
    public ?string $jobNo = null;
    public string $receiptNumber;

    public string $portalUrl;
    public string $trackingUrl;
    public string $loginUrl;
    public string $registerUrl;

    public function __construct($repairs, ?Pallet $pallet = null)
    {
        $this->repairs = $repairs instanceof RepairEntry
            ? collect([$repairs])
            : collect($repairs)->filter(fn($repair) => $repair instanceof RepairEntry)->values();

        $this->primaryRepair = $this->repairs->first();
        $this->pallet = $pallet ?? ($this->primaryRepair?->pallet_info ? Pallet::where('barcode_number', $this->primaryRepair->pallet_info)->first() : null);

        $firstJob = $this->pallet?->job_no ?? $this->repairs->pluck('job_no')->filter()->first();
        $this->jobNo = $firstJob ? trim((string) $firstJob) : null;

        $this->receiptNumber = !empty($this->jobNo)
            ? (preg_match('/^job/i', $this->jobNo) ? $this->jobNo : 'Job #: ' . $this->jobNo)
            : ('REP-' . now('America/Denver')->format('YmdHis'));

        $this->client = $this->pallet?->client ?? ($this->primaryRepair?->user_id ? \App\Models\Client::find($this->primaryRepair->user_id) : null);

        $baseUrl = rtrim(env('IT_RECOVERIES_URL', 'https://it-investmentrecoveries.com'), '/');
        $this->portalUrl = $baseUrl;
        $this->loginUrl = $baseUrl . '/login';
        $this->registerUrl = $baseUrl . '/register';
        $this->trackingUrl = $baseUrl . '/register';
    }

    public function build()
    {
        $barcode = $this->pallet?->barcode_number ?? $this->primaryRepair?->item_barcode ?? $this->primaryRepair?->pallet_info ?? 'Repair';
        $subjectRef = !empty($this->jobNo)
            ? (preg_match('/^job/i', $this->jobNo) ? $this->jobNo : 'Job #' . $this->jobNo)
            : $barcode;

        $subject = 'Repair Intake Receipt - ' . $subjectRef . ($this->primaryRepair?->customer_name ? ' (' . $this->primaryRepair->customer_name . ')' : '');

        return $this->from(
            config('mail.from.address', 'customerservice@it-investmentrecoveries.com'),
            config('mail.from.name', 'IT Investment Recoveries')
        )
            ->subject(trim($subject))
            ->view('emails.repair_details', [
                'repairs' => $this->repairs,
                'primaryRepair' => $this->primaryRepair,
                'pallet' => $this->pallet,
                'client' => $this->client,
                'jobNo' => $this->jobNo,
                'receiptNumber' => $this->receiptNumber,
                'portalUrl' => $this->portalUrl,
                'trackingUrl' => $this->trackingUrl,
                'loginUrl' => $this->loginUrl,
                'registerUrl' => $this->registerUrl,
            ]);
    }
}
