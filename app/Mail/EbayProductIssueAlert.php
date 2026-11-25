<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EbayProductIssueAlert extends Mailable
{
    use Queueable, SerializesModels;

    public array $product;
    public string $issueMessage;
    public string $customSubject;
    public ?string $reporterName;

    /**
     * Create a new message instance.
     */
    public function __construct(array $product, string $issueMessage = '', string $customSubject = '', ?string $reporterName = null)
    {
        $this->product = $product;
        $this->issueMessage = $issueMessage;
        $this->customSubject = $customSubject;
        $this->reporterName = $reporterName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', 'customerservice@it-investmentrecoveries.com');
        $fromName = config('mail.from.name', 'IT Investment Recoveries System');

        $prodNo = $this->product['product_no'] ?? $this->product['inventory_id'] ?? 'N/A';
        $prodName = $this->product['product_name'] ?? 'Product';

        $subject = !empty($this->customSubject)
            ? $this->customSubject
            : "[Product Issue Alert] Product #{$prodNo} - {$prodName}";

        return $this->from($fromAddress, $fromName)
            ->subject($subject)
            ->view('emails.ebay_product_issue_alert', [
                'product' => $this->product,
                'issueMessage' => $this->issueMessage,
                'reporterName' => $this->reporterName,
            ]);
    }
}
