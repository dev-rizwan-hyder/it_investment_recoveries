@extends('layouts.admin')

@section('content')
<div class="w-full animate-in fade-in duration-500 overflow-hidden relative">
    {{-- Page Heading --}}
    <div class="mb-6 p-2 md:p-4">
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <i class="fas fa-trash text-blue-600"></i> Universal Waste Entries
        </h1>
        <p class="text-slate-500 mt-1 text-sm">Track and manage universal waste items</p>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle mr-2 text-base"></i> {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-bold flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle mr-2 text-base text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Top Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 md:gap-4 mb-6" id="stats-container"></div>

    {{-- Action Toolbar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('admin.items-lot.intake') }}"
                class="bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center shadow-lg transition-all active:scale-95">
                <i class="fas fa-plus mr-2"></i> ADD INVENTORY
            </a>
            <button
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-copy mr-2"></i> Duplicate (0)
            </button>

            <button
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-tag mr-2"></i> Print Labels (0)
            </button>

            <button
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Print Invoice Receipts (0)
            </button>
            <button id="export-csv-btn"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-file-export mr-2"></i> Export CSV
            </button>
            <button
                class="bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center ml-auto transition-all shadow-lg shadow-blue-100">
                <i class="fas fa-sync-alt mr-2"></i> Sync to Shopify
            </button>
        </div>

        <div id="bulk-actions" class="hidden flex flex-wrap items-center gap-2 mb-4 pt-4 border-t border-slate-50">
            <span id="bulk-selected-count" class="text-[11px] font-black uppercase tracking-widest text-slate-500">0 selected</span>
            <select id="bulk-status-select"
                class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[170px]">
                <option value="">Change status...</option>
                <option value="Draft">Draft</option>
                <option value="Recieved">Recieved</option>
                <option value="Ready for Inventory">Ready for Inventory</option>
                <option value="In Progress">In Progress</option>
                <option value="Live Online">Live Online</option>
                <option value="Completed">Completed</option>
            </select>
            <button id="bulk-status-btn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-sync-alt mr-2"></i> Change Status
            </button>
            <button id="bulk-delete-btn"
                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-trash mr-2"></i> Delete Selected
            </button>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-50">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="search-input" type="text" placeholder="Search items..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/10">
            </div>

            <select id="status-filter"
                class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none">
                <option value="">All Statuses</option>
                <option value="Draft">Draft</option>
                <option value="Recieved">Recieved</option>
                <option value="Ready for Inventory">Ready for Inventory</option>
                <option value="In Progress">In Progress</option>
                <option value="Live Online">Live Online</option>
                <option value="Completed">Completed</option>
            </select>

            <select id="category-filter"
                class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none">
                <option value="">All Categories</option>
                <option value="Laptops">Laptops</option>
                <option value="Electronics">Electronics</option>
                <option value="Desktops">Desktops</option>
                <option value="Mixed Scrap">Mixed Scrap</option>
            </select>

            <select id="pallet-filter"
                class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[150px]">
                <option value="">All Pallets</option>
                @foreach ($pallets_list ?? [] as $pallet)
                    <option value="{{ $pallet->barcode_number }}">{{ $pallet->barcode_number }}</option>
                @endforeach
            </select>

            <button id="refresh-btn"
                class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all"><i
                    class="fas fa-redo-alt"></i></button>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm">
        <div class="overflow-x-auto cursor-grab select-none global-drag-scroll">
            <table class="w-full text-left border-collapse" style="min-width: 1280px;">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" id="select-all-items" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-4 w-28">Date</th>
                        <th class="px-4 py-4 w-36">Company</th>
                        <th class="px-4 py-4 w-36">Client</th>
                        <th class="px-4 py-4 w-36">Pallet #</th>
                        <th class="px-4 py-4 w-56">Name/Model</th>
                        <th class="px-4 py-4 w-32">Category</th>
                        <th class="px-4 py-4 w-28">Intake</th>
                        <th class="px-4 py-4 w-20 text-center">Photos</th>
                        <th class="px-4 py-4 w-24 text-center">Qty</th>
                        <th class="px-4 py-4" style="width: 11rem; min-width: 11rem;">Status</th>
                        <th class="px-4 py-4 w-28">Value</th>
                        <th class="px-4 py-4 w-32 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="items-table-body">
                    <tr>
                        <td colspan="13" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4" id="pagination-container"></div>
</div>

{{-- MODALS --}}
<datalist id="employees-list">
    @if (isset($employees))
        @foreach ($employees as $emp)
            <option value="{{ $emp->name }}"></option>
        @endforeach
    @endif
</datalist>

@include('admin.intake.modals.universal-waste.item-details-modal')
@include('admin.intake.modals.universal-waste.edit-item-modal')
@include('admin.intake.modals.add-to-inventory-modal')
@include('admin.intake.modals.partials.gallery-modal')

@push('scripts')
<script>
    const baseUrl = "{{ route('admin.universal-waste-entries.index') }}";
    const csrfToken = "{{ csrf_token() }}";
    const storageUrl = "/universal-waste";
</script>
<script src="{{ asset('js/admin/intake/universal-waste-entries.js') }}?v={{ @filemtime(public_path('js/admin/intake/universal-waste-entries.js')) }}"></script>
@endpush

@endsection
