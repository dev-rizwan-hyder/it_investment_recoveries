<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
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
        return $this->from('info@denverdiscountcomputers.com', 'Denver Discount Computers')
            ->subject('Your Denver Discount Computers Order Confirmation #' . $this->order->order_number)
            ->view('emails.order_confirmation');
    }
}
