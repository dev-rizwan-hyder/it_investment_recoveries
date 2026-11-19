<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DataDestructionItem;
use App\Models\EcommerceItem;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Order;
use App\Models\RefurbishingItem;
use App\Models\RepairEntry;
use App\Models\SalesInvoice;
use App\Models\UniversalWasteItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hasOrders = DB::getSchemaBuilder()->hasTable('orders');
        $hasSalesInvoices = DB::getSchemaBuilder()->hasTable('sales_invoices');
        $hasInventories = DB::getSchemaBuilder()->hasTable('inventories');
        $hasEcommerce = DB::getSchemaBuilder()->hasTable('ecommerce_items');
        $hasRefurbishing = DB::getSchemaBuilder()->hasTable('refurbishing_items');
        $hasDataDestruction = DB::getSchemaBuilder()->hasTable('data_destruction_items');
        $hasUniversalWaste = DB::getSchemaBuilder()->hasTable('universal_waste_items');
        $hasRepairEntries = DB::getSchemaBuilder()->hasTable('repair_entries');
        $hasExpenses = DB::getSchemaBuilder()->hasTable('expenses');
        $hasClients = DB::getSchemaBuilder()->hasTable('clients');
        $hasEmployees = DB::getSchemaBuilder()->hasTable('employees');

        // 1. Revenue & Financial Overview
        $storeRevenue = $hasOrders ? Order::whereNotIn('status', ['cancelled'])->sum('total') : 0;
        $paidInvoicesRevenue = $hasSalesInvoices ? SalesInvoice::where('status', 'paid')->sum('total') : 0;
        $totalRevenue = $storeRevenue + $paidInvoicesRevenue;

        $todayStoreRevenue = $hasOrders ? Order::whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', Carbon::today())
            ->sum('total') : 0;

        $pendingInvoicesCount = $hasSalesInvoices ? SalesInvoice::whereIn('status', ['pending', 'unpaid', 'draft'])->count() : 0;
        $pendingInvoicesAmount = $hasSalesInvoices ? SalesInvoice::whereIn('status', ['pending', 'unpaid', 'draft'])->sum('total') : 0;

        // 2. Inventory Metrics
        $inventoryTotalItems = $hasInventories ? Inventory::count() : 0;
        $inventoryTotalUnits = $hasInventories ? (int) Inventory::sum('quantity') : 0;
        $inventoryTotalValuation = $hasInventories ? (float) Inventory::sum(DB::raw('COALESCE(estimated_value, 0) * CASE WHEN COALESCE(quantity, 0) = 0 THEN 1 ELSE quantity END')) : 0.0;

        $inventoryStatusCounts = [
            'in_stock' => $hasInventories ? Inventory::where('status', 'In Stock')->count() : 0,
            'live_online' => $hasInventories ? Inventory::where('status', 'LIVE ONLINE')->count() : 0,
            'in_progress' => $hasInventories ? Inventory::where('status', 'In Progress')->count() : 0,
            'completed' => $hasInventories ? Inventory::where('status', 'Completed')->count() : 0,
            'pending' => $hasInventories ? Inventory::where('status', 'Pending')->count() : 0,
        ];

        // 3. Store Orders Metrics
        $ordersTotalCount = $hasOrders ? Order::count() : 0;
        $ordersTodayCount = $hasOrders ? Order::whereDate('created_at', Carbon::today())->count() : 0;
        $ordersCompletedCount = $hasOrders ? Order::whereIn('status', ['completed', 'shipped', 'delivered', 'in_store_purchased'])->count() : 0;
        $ordersPendingCount = $hasOrders ? Order::whereIn('status', ['pending', 'in_store_reserved', 'ready_for_pickup'])->count() : 0;

        // 4. Intake Streams
        $intakeCounts = [
            'ecommerce' => $hasEcommerce ? EcommerceItem::count() : 0,
            'refurbishing' => $hasRefurbishing ? RefurbishingItem::count() : 0,
            'data_destruction' => $hasDataDestruction ? DataDestructionItem::count() : 0,
            'universal_waste' => $hasUniversalWaste ? UniversalWasteItem::count() : 0,
        ];

        $intakeUnits = [
            'ecommerce' => $hasEcommerce ? (int) EcommerceItem::sum('quantity') : 0,
            'refurbishing' => $hasRefurbishing ? (int) RefurbishingItem::sum('quantity') : 0,
            'data_destruction' => $hasDataDestruction ? (int) DataDestructionItem::sum('quantity') : 0,
            'universal_waste' => $hasUniversalWaste ? (int) UniversalWasteItem::sum('quantity') : 0,
        ];

        $totalIntakeRecords = array_sum($intakeCounts);
        $totalIntakeUnits = array_sum($intakeUnits);

        // 5. Repair Pipeline & Operations
        $hasRepairStatusColumn = $hasRepairEntries && DB::getSchemaBuilder()->hasColumn('repair_entries', 'repair_status');
        $repairs = [
            'total' => $hasRepairEntries ? RepairEntry::count() : 0,
            'pending' => $hasRepairEntries ? RepairEntry::where(function($q) use ($hasRepairStatusColumn) {
                $q->where('status', 'pending');
                if ($hasRepairStatusColumn) {
                    $q->orWhere('repair_status', 'pending');
                }
            })->count() : 0,
            'in_progress' => $hasRepairEntries ? RepairEntry::where(function($q) use ($hasRepairStatusColumn) {
                $q->where('status', 'in_progress');
                if ($hasRepairStatusColumn) {
                    $q->orWhere('repair_status', 'in_progress');
                }
            })->count() : 0,
            'completed' => $hasRepairEntries ? RepairEntry::where(function($q) use ($hasRepairStatusColumn) {
                $q->where('status', 'completed');
                if ($hasRepairStatusColumn) {
                    $q->orWhere('repair_status', 'completed');
                }
            })->count() : 0,
        ];

        $operations = [
            'total_expenses' => $hasExpenses ? (float) Expense::sum('amount') : 0.0,
            'total_clients' => $hasClients ? Client::count() : 0,
            'total_employees' => $hasEmployees ? Employee::count() : 0,
        ];

        // 6. Recent Activity & Orders Feed
        $recentOrders = $hasOrders ? Order::latest()->take(6)->get() : collect();

        // 7. Monthly Revenue & Orders Chart Data (Past 6 Months)
        $chartMonths = [];
        $chartRevenue = [];
        $chartOrders = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartMonths[] = $date->format('M Y');

            $monthRev = $hasOrders ? Order::whereNotIn('status', ['cancelled'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total') : 0;

            $monthOrd = $hasOrders ? Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count() : 0;

            $chartRevenue[] = round((float) $monthRev, 2);
            $chartOrders[] = (int) $monthOrd;
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todayStoreRevenue',
            'pendingInvoicesCount',
            'pendingInvoicesAmount',
            'inventoryTotalItems',
            'inventoryTotalUnits',
            'inventoryTotalValuation',
            'inventoryStatusCounts',
            'ordersTotalCount',
            'ordersTodayCount',
            'ordersCompletedCount',
            'ordersPendingCount',
            'intakeCounts',
            'intakeUnits',
            'totalIntakeRecords',
            'totalIntakeUnits',
            'repairs',
            'operations',
            'recentOrders',
            'chartMonths',
            'chartRevenue',
            'chartOrders'
        ));
    }

    public function sales()
    {
        // 1. Today's Stats
        $todayOrders = Item::where('status', 'sold')->whereDate('sold_at', Carbon::today())->count();
        $todayRevenue = Item::where('status', 'sold')->whereDate('sold_at', Carbon::today())->sum('price');
        $todayItems = Item::where('status', 'sold')->whereDate('sold_at', Carbon::today())->count();

        // 2. 30 Days Stats
        $totalOrders30Days = Item::where('status', 'sold')
            ->where('sold_at', '>=', Carbon::now()->subDays(30))->count();
        $totalRevenueAllTime = Item::where('status', 'sold')->sum('price');
        $totalItemsSold = Item::where('status', 'sold')->count();

        // Average Order Value (Safety check for division by zero)
        $avgOrderValue = $totalOrders30Days > 0 ? ($totalRevenueAllTime / $totalItemsSold) : 0;

        // 3. Recent Orders Table (Fetching last 10 sold items)
        $recentOrders = Item::where('status', 'sold')
            ->latest('sold_at')
            ->take(10)
            ->get();

        return view('admin.sales.index', compact(
            'todayOrders',
            'todayRevenue',
            'todayItems',
            'totalOrders30Days',
            'totalRevenueAllTime',
            'avgOrderValue',
            'totalItemsSold',
            'recentOrders'
        ));
    }
}