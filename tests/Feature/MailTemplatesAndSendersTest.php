<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Mail\OrderStatusUpdated;
use App\Mail\SalesInvoiceNotification;
use App\Mail\RepairStatusUpdated;
use App\Models\Order;
use App\Models\RepairEntry;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MailTemplatesAndSendersTest extends TestCase
{
    public function test_store_index_route_is_defined(): void
    {
        $this->assertTrue(Route::has('store.index'));
        $this->assertNotEmpty(route('store.index'));
    }

    public function test_order_confirmation_renders_and_has_valid_sender(): void
    {
        $order = new Order([
            'order_number' => 'ORD-TEST-100',
            'status' => 'processing',
            'placed_at' => now(),
        ]);
        $order->setRelation('items', collect([]));

        $mailable = new OrderConfirmation($order);
        $rendered = $mailable->render();

        $this->assertStringContainsString('ORD-TEST-100', $rendered);
        $this->assertStringContainsString('Visit Our Website', $rendered);

        $mailable->build();
        $this->assertNotEmpty($mailable->from);
        $this->assertNotEquals('info@denverdiscountcomputers.com', $mailable->from[0]['address'] ?? '');
    }

    public function test_order_status_updated_renders_and_has_valid_sender(): void
    {
        $order = new Order([
            'order_number' => 'ORD-TEST-200',
            'status' => 'completed',
            'placed_at' => now(),
        ]);
        $order->setRelation('items', collect([]));

        $mailable = new OrderStatusUpdated($order);
        $rendered = $mailable->render();

        $this->assertStringContainsString('ORD-TEST-200', $rendered);
        $this->assertStringContainsString('Visit Our Website', $rendered);

        $mailable->build();
        $this->assertNotEmpty($mailable->from);
        $this->assertNotEquals('info@denverdiscountcomputers.com', $mailable->from[0]['address'] ?? '');
    }

    public function test_repair_status_updated_renders_safely(): void
    {
        $repair = new RepairEntry([
            'id' => 999,
            'customer_name' => 'Test Customer',
            'status' => 'Ready for Pickup',
        ]);

        $mailable = new RepairStatusUpdated($repair, 'Processing');
        $rendered = $mailable->render();

        $this->assertStringContainsString('999', $rendered);
        $this->assertStringContainsString('Ready for Pickup', $rendered);
    }
}
