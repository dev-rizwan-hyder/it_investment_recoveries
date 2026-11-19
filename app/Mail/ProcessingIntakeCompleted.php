<?php

namespace App\Mail;

use App\Models\Pallet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProcessingIntakeCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public Pallet $pallet;
    public $client;
    public $relatedPallets;
    public array $totals;
    public bool $hasMultipleEntries;
    public string $displayBarcode;
    public string $portalUrl;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Pallet $pallet, $relatedPallets = null)
    {
        $this->pallet = $pallet;
        $this->client = $pallet->client;

        if ($relatedPallets === null) {
            $relatedPallets = method_exists($pallet, 'getRelatedEntries')
                ? $pallet->getRelatedEntries()
                : collect([$pallet]);
        }

        $this->relatedPallets = collect($relatedPallets)->values();
        $this->hasMultipleEntries = $this->relatedPallets->count() > 1;

        if ($this->hasMultipleEntries) {
            $this->displayBarcode = trim((string) ($pallet->barcode_number ?? 'Pallet'));
        } else {
            $this->displayBarcode = method_exists($pallet, 'getDisplayBarcode')
                ? $pallet->getDisplayBarcode()
                : ($pallet->barcode_number ?? 'Pallet');
        }

        $this->totals = [
            'count' => $this->relatedPallets->count(),
            'estimated_count' => (int) $this->relatedPallets->sum(fn ($p) => (int) ($p->estimated_count ?? 0)),
            'gross_weight' => (float) $this->relatedPallets->sum(fn ($p) => (float) ($p->gross_weight ?? 0)),
            'tare_weight' => (float) $this->relatedPallets->sum(fn ($p) => (float) ($p->tare_weight ?? 0)),
            'net_weight' => (float) $this->relatedPallets->sum(fn ($p) => (float) ($p->net_weight ?? 0)),
        ];

        $baseUrl = rtrim(env('IT_RECOVERIES_URL', 'https://it-investmentrecoveries.com'), '/');
        $this->portalUrl = $baseUrl;
        $this->loginUrl = $baseUrl . '/login';
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Processing Intake Completed - Pallet ' . $this->displayBarcode;
        if ($this->hasMultipleEntries) {
            $subject .= ' (All ' . $this->relatedPallets->count() . ' Entries Completed)';
        }

        return $this->from(
            config('mail.from.address', 'customerservice@it-investmentrecoveries.com'),
            config('mail.from.name', 'IT Investment Recoveries')
        )
            ->subject($subject)
            ->view('emails.processing_intake_completed');
    }
}
