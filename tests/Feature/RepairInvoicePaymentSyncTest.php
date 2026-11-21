<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\RepairEntry;
use App\Models\SalesInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RepairInvoicePaymentSyncTest extends TestCase
{
    use DatabaseTransactions, \Illuminate\Foundation\Testing\WithoutMiddleware;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@example.com'],
            [
                'name' => 'Admin Tester',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );
    }

    public function test_repair_intake_status_becomes_completed_when_invoice_created_as_paid(): void
    {
        $client = Client::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ]);

        $repair = RepairEntry::create([
            'user_id' => $this->adminUser->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john.doe@example.com',
            'customer_phone' => '1234567890',
            'device_type' => 'MacBook Pro',
            'status' => RepairEntry::STATUS_RECEIVING,
            'repair_status' => RepairEntry::STATUS_RECEIVING,
            'payment_status' => RepairEntry::PAYMENT_STATUS_PENDING,
            'technician' => 'Tech Dave',
            'price' => 150.00,
            'quantity' => 1,
            'item_barcode' => 'REP-TEST-1001',
        ]);

        $this->assertEquals(RepairEntry::STATUS_RECEIVING, $repair->status);
        $this->assertEquals(RepairEntry::PAYMENT_STATUS_PENDING, $repair->payment_status);

        $response = $this->actingAs($this->adminUser)->post(route('admin.sales.invoices.store'), [
            'repair_id' => $repair->id,
            'client_id' => $client->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john.doe@example.com',
            'customer_phone' => '1234567890',
            'service_date' => now()->format('Y-m-d'),
            'due_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'payment_type' => 'in_store',
            'status' => SalesInvoice::STATUS_PAID,
            'items' => [
                [
                    'invoice_category' => 'Repair Services',
                    'description' => 'MacBook Pro Screen Repair',
                    'quantity' => 1,
                    'unit_price' => 150.00,
                    'taxable' => 1,
                ]
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $repair->refresh();

        $this->assertEquals(RepairEntry::STATUS_COMPLETED, $repair->status);
        $this->assertEquals(RepairEntry::STATUS_COMPLETED, $repair->repair_status);
        $this->assertEquals(RepairEntry::PAYMENT_STATUS_PAID, $repair->payment_status);
    }

    public function test_repair_intake_status_becomes_completed_when_unpaid_invoice_is_updated_to_paid(): void
    {
        $client = Client::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '9876543210',
        ]);

        $repair = RepairEntry::create([
            'user_id' => $this->adminUser->id,
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane.smith@example.com',
            'device_type' => 'iPhone 13',
            'status' => RepairEntry::STATUS_PROCESSING,
            'repair_status' => RepairEntry::STATUS_PROCESSING,
            'payment_status' => RepairEntry::PAYMENT_STATUS_PENDING,
            'technician' => 'Tech Dave',
            'price' => 100.00,
            'quantity' => 1,
            'item_barcode' => 'REP-TEST-1002',
        ]);

        $invoice = SalesInvoice::create([
            'repair_entry_id' => $repair->id,
            'client_id' => $client->id,
            'created_by' => $this->adminUser->id,
            'invoice_number' => SalesInvoice::generateInvoiceNumber(),
            'service_date' => now()->format('Y-m-d'),
            'due_date' => now()->format('Y-m-d'),
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane.smith@example.com',
            'payment_method' => 'cash',
            'subtotal' => 100.00,
            'tax_rate' => 0.08,
            'tax_amount' => 8.00,
            'total' => 108.00,
            'status' => SalesInvoice::STATUS_UNPAID,
        ]);

        $repair->refresh();
        $this->assertEquals(RepairEntry::STATUS_PROCESSING, $repair->status);

        $response = $this->actingAs($this->adminUser)->put(route('admin.sales.invoices.update', $invoice), [
            'repair_id' => $repair->id,
            'client_id' => $client->id,
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane.smith@example.com',
            'service_date' => now()->format('Y-m-d'),
            'due_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'payment_type' => 'in_store',
            'status' => SalesInvoice::STATUS_PAID,
            'items' => [
                [
                    'invoice_category' => 'Repair Services',
                    'description' => 'iPhone 13 Screen Replacement',
                    'quantity' => 1,
                    'unit_price' => 100.00,
                    'taxable' => 1,
                ]
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $repair->refresh();

        $this->assertEquals(RepairEntry::STATUS_COMPLETED, $repair->status);
        $this->assertEquals(RepairEntry::STATUS_COMPLETED, $repair->repair_status);
        $this->assertEquals(RepairEntry::PAYMENT_STATUS_PAID, $repair->payment_status);
    }

    public function test_repair_intake_status_becomes_completed_via_square_webhook(): void
    {
        $client = Client::create([
            'name' => 'Alice Wonder',
            'email' => 'alice@example.com',
        ]);

        $repair = RepairEntry::create([
            'user_id' => $this->adminUser->id,
            'customer_name' => 'Alice Wonder',
            'customer_email' => 'alice@example.com',
            'device_type' => 'Dell XPS',
            'status' => RepairEntry::STATUS_RECEIVING,
            'repair_status' => RepairEntry::STATUS_RECEIVING,
            'payment_status' => RepairEntry::PAYMENT_STATUS_PENDING,
            'technician' => 'Tech Dave',
            'price' => 200.00,
            'quantity' => 1,
            'item_barcode' => 'REP-TEST-1003',
        ]);

        $invoice = SalesInvoice::create([
            'repair_entry_id' => $repair->id,
            'client_id' => $client->id,
            'created_by' => $this->adminUser->id,
            'invoice_number' => SalesInvoice::generateInvoiceNumber(),
            'service_date' => now()->format('Y-m-d'),
            'due_date' => now()->format('Y-m-d'),
            'customer_name' => 'Alice Wonder',
            'customer_email' => 'alice@example.com',
            'payment_method' => 'square_card',
            'subtotal' => 200.00,
            'tax_rate' => 0.08,
            'tax_amount' => 16.00,
            'total' => 216.00,
            'status' => SalesInvoice::STATUS_UNPAID,
            'square_invoice_id' => 'sq_inv_test_123',
            'square_status' => 'UNPAID',
        ]);

        $repair->refresh();
        $this->assertEquals(RepairEntry::STATUS_RECEIVING, $repair->status);

        $response = $this->postJson('/api/square/webhook', [
            'type' => 'invoice.payment_made',
            'data' => [
                'object' => [
                    'invoice' => [
                        'id' => 'sq_inv_test_123',
                        'status' => 'PAID',
                        'public_url' => 'https://square.link/i/sq_inv_test_123',
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);

        $repair->refresh();
        $this->assertEquals(RepairEntry::STATUS_COMPLETED, $repair->status);
        $this->assertEquals(RepairEntry::STATUS_COMPLETED, $repair->repair_status);
        $this->assertEquals(RepairEntry::PAYMENT_STATUS_PAID, $repair->payment_status);
    }
}
