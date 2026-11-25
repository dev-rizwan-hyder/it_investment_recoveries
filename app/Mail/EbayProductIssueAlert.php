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
    public ?string $replyToEmail;
    public string $recipientDisplay;
    public bool $isBlinking;

    /**
     * Create a new message instance.
     */
    public function __construct(
        array $product,
        string $issueMessage = '',
        string $customSubject = '',
        ?string $reporterName = null,
        ?string $replyToEmail = null,
        string $recipientDisplay = 'sal@it-investmentrecoveries.com',
        bool $isBlinking = true
    ) {
        $this->product = $product;
        $this->issueMessage = $issueMessage;
        $this->customSubject = $customSubject;
        $this->reporterName = $reporterName;
        $this->replyToEmail = $replyToEmail;
        $this->recipientDisplay = $recipientDisplay;
        $this->isBlinking = $isBlinking;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $configuredFrom = trim((string) config('mail.from.address', ''));
        $smtpUsername = trim((string) config('mail.mailers.smtp.username', ''));
        $fromAddress = ($configuredFrom !== '' && $configuredFrom !== 'hello@example.com' && filter_var($configuredFrom, FILTER_VALIDATE_EMAIL))
            ? $configuredFrom
            : (filter_var($smtpUsername, FILTER_VALIDATE_EMAIL) ? $smtpUsername : 'customerservice@it-investmentrecoveries.com');

        $fromName = trim((string) config('mail.from.name', '')) ?: 'IT Investment Recoveries System';

        $prodNo = $this->product['product_no'] ?? $this->product['inventory_id'] ?? 'N/A';
        $prodName = $this->product['product_name'] ?? 'Product';

        $subject = !empty($this->customSubject)
            ? $this->customSubject
            : "[Product Issue Alert] Product #{$prodNo} - {$prodName}";

        $mail = $this->from($fromAddress, $fromName)
            ->subject($subject)
            ->view('emails.ebay_product_issue_alert', [
                'product' => $this->product,
                'issueMessage' => $this->issueMessage,
                'reporterName' => $this->reporterName,
                'recipientDisplay' => $this->recipientDisplay,
                'isBlinking' => $this->isBlinking,
            ]);

        if (!empty($this->replyToEmail) && filter_var($this->replyToEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($this->replyToEmail, $this->reporterName ?: $this->replyToEmail);
        }

        return $mail;
    }
}
