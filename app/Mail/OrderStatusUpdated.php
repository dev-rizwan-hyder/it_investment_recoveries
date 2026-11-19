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
        return $this->from('info@denverdiscountcomputers.com', 'Denver Discount Computers')
            ->subject('Update on your Denver Discount Computers order ' . $this->order->order_number)
            ->view('emails.order_status_update');
    }
}
