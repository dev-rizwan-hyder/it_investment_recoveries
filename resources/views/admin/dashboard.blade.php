@extends('layouts.admin')

@section('content')
<div class="max-w-[1600px] mx-auto p-4 lg:p-8 space-y-8">
    {{-- Top Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 p-6 md:p-8 rounded-3xl text-white shadow-2xl relative overflow-hidden">
        <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-full text-xs font-extrabold uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                    Live Platform Analytics
                </span>
                <span class="text-slate-400 text-xs font-semibold hidden sm:inline">• {{ now()->format('l, F j, Y') }}</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white">Executive Operations Dashboard</h1>
            <p class="text-slate-300 text-sm md:text-base font-medium mt-1">Welcome back, <span class="text-blue-400 font-bold">{{ Auth::user()->name }}</span>. Real-time overview of financial performance, inventory valuation, and processing streams.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full lg:w-auto relative z-10">
            <a href="{{ route('admin.sales.invoices.create') }}" class="flex-1 lg:flex-none bg-blue-600 hover:bg-blue-500 text-white px-5 py-3 rounded-2xl font-bold text-xs uppercase tracking-wider shadow-lg shadow-blue-600/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-file-invoice"></i> Create Invoice
            </a>
            <a href="{{ route('admin.items-lot.intake') }}" class="flex-1 lg:flex-none bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 px-5 py-3 rounded-2xl font-bold text-xs uppercase tracking-wider shadow-md hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle text-blue-400"></i> Stock Intake
            </a>
        </div>
    </div>

    {{-- Executive Top KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Total Gross Revenue --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl hover:border-emerald-200 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-wider px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">
                    Gross Revenue
                </span>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Sales & Revenue</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">${{ number_format($totalRevenue, 2) }}</h3>
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Store Sales + Paid Invoices</span>
                <span class="font-bold text-emerald-600">${{ number_format($todayStoreRevenue, 2) }} today</span>
            </div>
        </div>

        {{-- Active Inventory Valuation --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl hover:border-blue-200 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3.5 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-wider px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                    Asset Valuation
                </span>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Inventory Value</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">${{ number_format($inventoryTotalValuation, 2) }}</h3>
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">{{ $inventoryTotalItems }} Catalog Items</span>
                <span class="font-bold text-blue-600">{{ number_format($inventoryTotalUnits) }} Units</span>
            </div>
        </div>

        {{-- Store Orders --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl hover:border-purple-200 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl group-hover:bg-purple-600 group-hover:text-white transition-all shadow-sm">
                    <i class="fas fa-shopping-bag text-xl"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-wider px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full border border-purple-100">
                    Order Volume
                </span>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Store Orders</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">{{ $ordersTotalCount }}</h3>
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="font-bold text-emerald-600">{{ $ordersCompletedCount }} Dispatched</span>
                <span class="font-bold text-orange-600">{{ $ordersPendingCount }} Active</span>
            </div>
        </div>

        {{-- Intake & Processing Stream --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl hover:border-amber-200 transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3.5 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-600 group-hover:text-white transition-all shadow-sm">
                    <i class="fas fa-recycle text-xl"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-wider px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-100">
                    Intake Hub
                </span>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Intake Records</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">{{ $totalIntakeRecords }}</h3>
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">4 Processing Streams</span>
                <span class="font-bold text-amber-600">{{ number_format($totalIntakeUnits) }} Physical Units</span>
            </div>
        </div>
    </div>

    {{-- Interactive Visual Analytics Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue & Order Trend Chart --}}
        <div class="lg:col-span-2 bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Revenue & Orders Performance</h3>
                    <p class="text-xs text-slate-400 font-medium">Monthly revenue trends and total order volumes over the past 6 months.</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-bold">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <span class="text-slate-600">Revenue ($)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="text-slate-600">Orders</span>
                    </div>
                </div>
            </div>
            <div class="h-[300px] w-full relative">
                <canvas id="revenueOrdersChart"></canvas>
            </div>
        </div>

        {{-- Processing & Intake Streams Doughnut Chart --}}
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Intake Stream Distribution</h3>
                <p class="text-xs text-slate-400 font-medium mb-6">Proportion of processed items across modules.</p>
                <div class="h-[220px] w-full relative flex items-center justify-center">
                    <canvas id="intakeStreamChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-6 pt-4 border-t border-slate-100 text-xs">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-blue-50/50">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> E-Commerce
                    </span>
                    <span class="font-black text-blue-600">{{ $intakeCounts['ecommerce'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50/50">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Refurbishing
                    </span>
                    <span class="font-black text-amber-600">{{ $intakeCounts['refurbishing'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50/50">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Destruction
                    </span>
                    <span class="font-black text-rose-600">{{ $intakeCounts['data_destruction'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-purple-50/50">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Waste
                    </span>
                    <span class="font-black text-purple-600">{{ $intakeCounts['universal_waste'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Launcher --}}
    <div>
        <h3 class="text-base font-black text-slate-800 uppercase tracking-wider mb-4 px-1">Quick Operational Workflows</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.items-lot.intake') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-lg transition-all text-center flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center mb-3 transition-all shadow-sm">
                    <i class="fas fa-boxes text-lg"></i>
                </div>
                <span class="text-xs font-black text-slate-800">Items/Lot Intake</span>
            </a>

            <a href="{{ route('admin.processing-intake.index') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 hover:border-indigo-500 hover:shadow-lg transition-all text-center flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center mb-3 transition-all shadow-sm">
                    <i class="fas fa-microchip text-lg"></i>
                </div>
                <span class="text-xs font-black text-slate-800">Processing Hub</span>
            </a>

            <a href="{{ route('admin.sales.invoices') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:shadow-lg transition-all text-center flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center mb-3 transition-all shadow-sm">
                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                </div>
                <span class="text-xs font-black text-slate-800">Sales & Invoices</span>
            </a>

            <a href="{{ route('admin.inventory.index') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 hover:border-sky-500 hover:shadow-lg transition-all text-center flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 group-hover:bg-sky-600 group-hover:text-white flex items-center justify-center mb-3 transition-all shadow-sm">
                    <i class="fas fa-warehouse text-lg"></i>
                </div>
                <span class="text-xs font-black text-slate-800">Full Inventory</span>
            </a>

            <a href="{{ route('admin.data-destruction-entries.index') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 hover:border-rose-500 hover:shadow-lg transition-all text-center flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center mb-3 transition-all shadow-sm">
                    <i class="fas fa-eraser text-lg"></i>
                </div>
                <span class="text-xs font-black text-slate-800">Data Destruction</span>
            </a>
        </div>
    </div>

    {{-- Bottom Grid: Recent Store Orders & Operations Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Store Orders Table --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col justify-between">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Recent Store Transactions</h3>
                    <p class="text-xs text-slate-400 font-medium">Latest customer orders processed through store and pickup.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    View All Orders <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">Order Number</th>
                            <th class="py-3.5 px-6">Customer</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Total Amount</th>
                            <th class="py-3.5 px-6 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                {{ $order->order_number }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $order->customer_name ?? 'Guest Customer' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->customer_email ?? 'No email' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusClasses = [
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'shipped' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'in_store_purchased' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'in_store_reserved' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'ready_for_pickup' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'pending' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                    $badgeStyle = $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeStyle }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-black text-slate-900">
                                ${{ number_format($order->total, 2) }}
                            </td>
                            <td class="py-4 px-6 text-right text-slate-400 font-medium text-[11px]">
                                {{ $order->created_at ? $order->created_at->format('M d, H:i') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-medium">No recent orders recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Showing last {{ count($recentOrders) }} transactions</span>
                <span class="font-bold text-slate-700">Total Active Orders: {{ $ordersTotalCount }}</span>
            </div>
        </div>

        {{-- Operations & System Health Card --}}
        <div class="space-y-6">
            {{-- Inventory Status Breakdown --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Inventory Health Status</h3>
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-xl text-xs"><i class="fas fa-cubes"></i></span>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                        <span class="font-bold text-slate-700 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> In Stock
                        </span>
                        <span class="font-black text-slate-900">{{ $inventoryStatusCounts['in_stock'] }} items</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                        <span class="font-bold text-slate-700 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Live Online
                        </span>
                        <span class="font-black text-slate-900">{{ $inventoryStatusCounts['live_online'] }} items</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                        <span class="font-bold text-slate-700 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> In Progress
                        </span>
                        <span class="font-black text-slate-900">{{ $inventoryStatusCounts['in_progress'] }} items</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                        <span class="font-bold text-slate-700 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Pending Setup
                        </span>
                        <span class="font-black text-slate-900">{{ $inventoryStatusCounts['pending'] }} items</span>
                    </div>
                </div>
            </div>

            {{-- Operations & Financial Metrics Summary --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 rounded-3xl shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                    <span class="text-xs font-bold text-slate-300">Pending Invoices</span>
                    <span class="text-sm font-black text-amber-400">${{ number_format($pendingInvoicesAmount, 2) }} <span class="text-[10px] text-slate-400">({{ $pendingInvoicesCount }})</span></span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                    <span class="text-xs font-bold text-slate-300">Recorded Expenses</span>
                    <span class="text-sm font-black text-rose-400">${{ number_format($operations['total_expenses'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                    <span class="text-xs font-bold text-slate-300">Registered Clients</span>
                    <span class="text-sm font-black text-blue-400">{{ $operations['total_clients'] }} Clients</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs font-bold text-slate-300">Active Staff</span>
                    <span class="text-sm font-black text-emerald-400">{{ $operations['total_employees'] }} Employees</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue & Orders Trend Chart
    const ctxRevenue = document.getElementById('revenueOrdersChart').getContext('2d');
    
    const revGradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    revGradient.addColorStop(0, 'rgba(37, 99, 235, 0.35)');
    revGradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartMonths) !!},
            datasets: [
                {
                    label: 'Gross Revenue ($)',
                    data: {!! json_encode($chartRevenue) !!},
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    backgroundColor: revGradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Order Count',
                    data: {!! json_encode($chartOrders) !!},
                    borderColor: '#34d399',
                    backgroundColor: '#34d399',
                    borderRadius: 6,
                    type: 'bar',
                    barThickness: 16,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    padding: 12,
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                    bodyFont: { family: 'Inter', size: 11 },
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label.includes('Revenue')) {
                                return context.dataset.label + ': $' + context.raw.toLocaleString();
                            }
                            return context.dataset.label + ': ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11, weight: 'bold' }, color: '#64748b' }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { family: 'Inter', size: 10 },
                        color: '#64748b',
                        callback: function(value) { return '$' + value; }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 10 },
                        color: '#64748b',
                        stepSize: 1
                    }
                }
            }
        }
    });

    // 2. Intake Stream Doughnut Chart
    const ctxIntake = document.getElementById('intakeStreamChart').getContext('2d');
    new Chart(ctxIntake, {
        type: 'doughnut',
        data: {
            labels: ['E-Commerce', 'Refurbishing', 'Data Destruction', 'Universal Waste'],
            datasets: [{
                data: [
                    {{ $intakeCounts['ecommerce'] }},
                    {{ $intakeCounts['refurbishing'] }},
                    {{ $intakeCounts['data_destruction'] }},
                    {{ $intakeCounts['universal_waste'] }}
                ],
                backgroundColor: [
                    '#3b82f6',
                    '#f59e0b',
                    '#f43f5e',
                    '#a855f7'
                ],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    padding: 10,
                    backgroundColor: '#0f172a',
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' items';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection