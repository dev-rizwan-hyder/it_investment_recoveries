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

    public function __construct($repairs, ?Pallet $pallet = null)
    {
        $this->repairs = $repairs instanceof RepairEntry
            ? collect([$repairs])
            : collect($repairs)->filter(fn($repair) => $repair instanceof RepairEntry)->values();

        $this->primaryRepair = $this->repairs->first();
        $this->pallet = $pallet ?? ($this->primaryRepair?->pallet_info ? Pallet::where('barcode_number', $this->primaryRepair->pallet_info)->first() : null);
    }

    public function build()
    {
        $jobNo = $this->pallet?->job_no;
        $subject = 'Repair Intake Details' . ($jobNo ? ' - Job #' . $jobNo : '') . ($this->primaryRepair?->customer_name ? ' (' . $this->primaryRepair->customer_name . ')' : '');

        return $this->from(config('mail.from.address', 'info@denverdiscountcomputers.com'), config('mail.from.name', 'Denver Discount Computers'))
            ->subject(trim($subject))
            ->view('emails.repair_details', [
                'repairs' => $this->repairs,
                'primaryRepair' => $this->primaryRepair,
                'pallet' => $this->pallet,
            ]);
    }
}
