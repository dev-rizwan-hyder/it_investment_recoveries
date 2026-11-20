<?php

namespace App\Mail;

use App\Models\RepairEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RepairStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public RepairEntry $repair;
    public ?string $previousStatus;

    public function __construct(RepairEntry $repair, ?string $previousStatus = null)
    {
        $this->repair = $repair->loadMissing(['client', 'user', 'pallet']);
        $this->previousStatus = $previousStatus;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address') ?: 'customerservice@it-investmentrecoveries.com';
        $fromName = config('mail.from.name') ?: 'IT Investment Recoveries';

        $deviceTitle = trim(($this->repair->brand_category ? $this->repair->brand_category . ' ' : '') . ($this->repair->device_type ?: 'Repair Item'));
        $jobSuffix = $this->repair->job_no ? ' (Job #' . $this->repair->job_no . ')' : '';
        $subject = 'Repair Status Update: ' . $this->repair->status_label . $jobSuffix . ' - ' . $fromName;

        return $this->from($fromAddress, $fromName)
            ->subject($subject)
            ->view('emails.repair_status_update', [
                'repair' => $this->repair,
                'previousStatus' => $this->previousStatus,
            ]);
    }
}
