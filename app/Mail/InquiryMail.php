<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public string $formType;
    public string $emailSubject;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->formType = $data['form_type'] ?? 'general';

        if ($this->formType === 'pickup') {
            $sender = !empty($data['business_name']) ? $data['business_name'] : ($data['name'] ?? 'Website User');
            $this->emailSubject = "[Pickup Inquiry] New Request from " . $sender;
        } else {
            $sender = $data['name'] ?? 'Website User';
            $this->emailSubject = "[General Inquiry] New Message from " . $sender;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $replyToEmail = filter_var($this->data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $replyToName = $this->data['name'] ?? 'Inquirer';

        $envelope = new Envelope(
            subject: $this->emailSubject,
        );

        if ($replyToEmail) {
            $envelope->replyTo = [
                new Address($replyToEmail, $replyToName),
            ];
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry',
            with: [
                'data' => $this->data,
                'formType' => $this->formType,
                'emailSubject' => $this->emailSubject,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
