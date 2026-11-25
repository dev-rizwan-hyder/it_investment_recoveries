<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->load('items');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address') ?: (config('mail.mailers.smtp.username') ?: 'customerservice@it-investmentrecoveries.com');
        $fromName = config('mail.from.name') ?: 'Denver Discount Computers';

        return $this->from($fromAddress, $fromName)
            ->subject('Update on your Denver Discount Computers order ' . $this->order->order_number)
            ->view('emails.order_status_update');
    }
}
