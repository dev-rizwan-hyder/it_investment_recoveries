<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryZeroQuantityStatusTest extends TestCase
{
    use DatabaseTransactions, \Illuminate\Foundation\Testing\WithoutMiddleware;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_inventory_test@example.com'],
            [
                'name' => 'Admin Inventory Tester',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );
    }

    public function test_creating_or_updating_inventory_to_zero_quantity_sets_status_to_out_of_stock(): void
    {
        $created = Inventory::create([
            'barcode' => 'TEST-ZERO-' . uniqid(),
            'name' => 'Test Zero Qty Item',
            'category' => 'Laptops',
            'quantity' => 0,
            'status' => 'In Stock',
        ]);

        $this->assertSame('Out of Stock', $created->status);

        $item = Inventory::create([
            'barcode' => 'TEST-UPD-' . uniqid(),
            'name' => 'Test Update Qty Item',
            'category' => 'Laptops',
            'quantity' => 3,
            'status' => 'In Stock',
        ]);

        $this->assertSame('In Stock', $item->status);

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.inventory.update', $item->id), [
            'name' => $item->name,
            'category' => $item->category,
            'quantity' => 0,
            'status' => 'In Stock',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $item->refresh();
        $this->assertSame(0, (int) $item->quantity);
        $this->assertSame('Out of Stock', $item->status);
    }

    public function test_direct_sell_to_zero_quantity_sets_status_to_out_of_stock(): void
    {
        $item = Inventory::create([
            'barcode' => 'TEST-SELL-' . uniqid(),
            'name' => 'Test Direct Sell Item',
            'category' => 'Monitors',
            'device_type' => 'Monitor',
            'quantity' => 2,
            'reuse_quantity' => 0,
            'price' => 50.00,
            'status' => 'In Stock',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.inventory.direct-sell', $item->id), [
            'quantity' => 2,
            'customer_name' => 'Test Buyer',
            'customer_email' => '',
            'customer_phone' => '',
            'payment_method' => 'cash',
            'order_status' => Order::STATUS_IN_STORE_PURCHASED,
            'sale_type' => 'direct',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $item->refresh();
        $this->assertSame(0, (int) $item->quantity);
        $this->assertSame('Out of Stock', $item->status);
    }

    public function test_status_update_and_filter_only_accept_in_stock_and_out_of_stock(): void
    {
        $item = Inventory::create([
            'barcode' => 'TEST-STAT-' . uniqid(),
            'name' => 'Test Status Filter Item',
            'category' => 'Desktops',
            'quantity' => 5,
            'status' => 'In Stock',
        ]);

        $this->actingAs($this->adminUser)
            ->postJson(route('admin.inventory.update-status', $item->id), ['status' => 'COMPLETED'])
            ->assertStatus(422);

        $this->actingAs($this->adminUser)
            ->postJson(route('admin.inventory.update-status', $item->id), ['status' => 'Out of Stock'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $filterResponse = $this->actingAs($this->adminUser)
            ->getJson(route('admin.inventory.load-data', ['statusFilter' => 'Out of Stock', 'search' => $item->barcode]));

        $filterResponse->assertOk()->assertJson(['success' => true, 'total' => 1]);
    }
}
