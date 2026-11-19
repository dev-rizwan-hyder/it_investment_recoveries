<?php

namespace App\Mail;

use App\Models\SalesInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalesInvoiceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public SalesInvoice $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(SalesInvoice $invoice)
    {
        $this->invoice = $invoice->loadMissing(['items', 'client', 'creator']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', 'info@denverdiscountcomputers.com');
        $fromName = config('mail.from.name', 'Denver Discount Computers');
        $subject = 'Invoice #' . $this->invoice->invoice_number . ' - ' . $fromName;

        $email = $this->from($fromAddress, $fromName)
            ->subject($subject)
            ->view('emails.sales_invoice', [
                'invoice' => $this->invoice,
            ]);

        // Attach pictures or documents if present and within reasonable size limits
        if (!empty($this->invoice->picture_paths)) {
            $totalAttachmentSize = 0;
            $maxAttachmentBytes = 15 * 1024 * 1024; // 15MB limit

            foreach ($this->invoice->picture_paths as $path) {
                $fileName = SalesInvoice::fileName($path);

                if (str_starts_with($path, 'data:')) {
                    if (preg_match('/^data:([^;]+)(?:;name=[^;]+)?;base64,(.+)$/s', $path, $matches)) {
                        $mimeType = $matches[1];
                        $data = base64_decode($matches[2]);
                        $size = strlen($data);

                        if (($totalAttachmentSize + $size) <= $maxAttachmentBytes) {
                            $totalAttachmentSize += $size;
                            $email->attachData($data, $fileName, ['mime' => $mimeType]);
                        }
                    }
                } else {
                    $localPath = public_path(ltrim((string) $path, '/'));
                    if (file_exists($localPath)) {
                        $size = filesize($localPath);
                        if (($totalAttachmentSize + $size) <= $maxAttachmentBytes) {
                            $totalAttachmentSize += $size;
                            $email->attach($localPath, ['as' => $fileName]);
                        }
                    }
                }
            }
        }

        return $email;
    }
}
