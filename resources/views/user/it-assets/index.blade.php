@extends('user.layouts.app')

@section('title', 'IT Assets')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Session Flash Notifications --}}
    @if (session('success'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Header Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-indigo-600/30 to-purple-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-cyan-500/20 to-blue-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-400/30 text-xs font-bold uppercase tracking-widest text-indigo-300">
                        <i class="fa-solid fa-laptop-code mr-1.5 text-indigo-400"></i> Asset Management
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Linked: {{ $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    IT Asset Management
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Monitor inventory, technical specifications and serial numbers status of your processed IT equipment.
                </p>
            </div>
        </div>
    </section>

    {{-- Stats Cards Grid --}}
    @if ($stats)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Card 1: Recieved Assets / Pallet -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Received Assets / Pallet</p>
                        <p class="mt-1.5 text-2xl font-black text-blue-600">
                            {{ $stats['receivedCount'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </span>
                </div>
            </div>

            <!-- Card 2: Processing Recieved Pallet -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Processing Recieved Pallet</p>
                        <p class="mt-1.5 text-2xl font-black text-amber-600">
                            {{ $stats['processingCount'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-100">
                        <i class="fa-solid fa-gears text-lg"></i>
                    </span>
                </div>
            </div>

            <!-- Card 3: Completed Assets / Pallets -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Completed Assets / Pallets</p>
                        <p class="mt-1.5 text-2xl font-black text-emerald-600">
                            {{ $stats['completedCount'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </span>
                </div>
            </div>
        </section>
    @endif

    {{-- Main Table Section --}}
    <section class="portal-card rounded-3xl overflow-hidden">
        <!-- Search & Filter Bar -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col lg:flex-row gap-4 justify-between items-center">
            <!-- Text Search Input -->
            <div class="relative w-full lg:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" 
                       id="assetSearchInput" 
                       placeholder="Search barcode, asset name, serial..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition">
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <!-- Status Filter Dropdown -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="assetStatusFilter" class="text-sm font-bold text-slate-600 whitespace-nowrap">Status:</label>
                    <select id="assetStatusFilter" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition w-full sm:w-auto">
                        <option value="all">All Statuses</option>
                        <option value="received">RECEIVED</option>
                        <option value="processing">PROCESSING</option>
                        <option value="completed">COMPLETED</option>
                    </select>
                </div>

                <!-- Date Filter Input -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="assetDateFilter" class="text-sm font-bold text-slate-600 whitespace-nowrap">Received Date:</label>
                    <input type="date" 
                           id="assetDateFilter" 
                           class="px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition w-full sm:w-auto">
                </div>

                <!-- Reset Filters Button -->
                <button type="button" 
                        id="resetAssetFiltersBtn"
                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 rounded-xl text-sm font-bold transition flex items-center gap-1.5"
                        title="Clear all filters">
                    <i class="fa-solid fa-arrow-rotate-left text-xs"></i> Reset
                </button>
            </div>
        </div>

        <!-- Bulk Action Floating / Sticky Toolbar -->
        <div id="bulkActionToolbar" class="hidden border-b border-indigo-100 bg-indigo-50/90 px-6 py-3.5 flex flex-wrap items-center justify-between gap-4 transition-all">
            <div class="flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-white font-extrabold text-xs">
                    <i class="fa-solid fa-check-double text-xs"></i>
                </span>
                <span id="selectedCountText" class="text-sm font-extrabold text-indigo-950">0 items selected</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="deselectAllBtn" class="px-3.5 py-1.5 rounded-xl border border-indigo-200 bg-white hover:bg-slate-50 text-sm font-bold text-slate-700 transition shadow-2xs">
                    Deselect All
                </button>
                <button type="button" id="bulkDeleteBtn" class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-sm font-bold text-white transition shadow-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i> Delete Selected (<span id="bulkDeleteBtnCount">0</span>)
                </button>
            </div>
        </div>

        @if ($items->count() > 0)

            <div id="tableScrollWrapper" class="overflow-x-auto cursor-grab active:cursor-grabbing select-none transition-colors">
                <table class="w-full text-left border-collapse min-w-[1350px]">
                    <thead>
                        <tr class="text-xs sm:text-[13px] uppercase font-black text-slate-500 tracking-wider bg-slate-50/70 border-b border-slate-200">
                            <th class="px-4 py-4 w-10 text-center select-none">
                                <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer" title="Select All Visible">
                            </th>
                            <th class="px-6 py-4 min-w-[170px] whitespace-nowrap">Barcode #</th>
                            <th class="px-6 py-4 min-w-[200px]">Asset Name</th>
                            <th class="px-6 py-4 min-w-[190px]">Brand / Model</th>
                            <th class="px-6 py-4 min-w-[130px] whitespace-nowrap">Category</th>
                            <th class="px-6 py-4 text-center min-w-[140px] whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-center min-w-[140px] whitespace-nowrap">Qty / Weight</th>
                            <th class="px-6 py-4 min-w-[170px]">Serial Numbers</th>
                            <th class="px-6 py-4 min-w-[140px] whitespace-nowrap">Received Date</th>
                            <th class="px-6 py-4 text-right min-w-[180px] whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="assetTableBody" class="text-sm font-bold text-slate-800 divide-y divide-slate-100">
                        @foreach ($items as $item)
                            @php
                                $rawStatus = strtolower(trim($item->status ?? ''));
                                if (str_contains($rawStatus, 'complet') || str_contains($rawStatus, 'ready')) {
                                    $statusDisplay = 'COMPLETED';
                                    $statusStyle = 'bg-[#10b981] text-white';
                                } elseif (str_contains($rawStatus, 'progr') || str_contains($rawStatus, 'process')) {
                                    $statusDisplay = 'PROCESSING';
                                    $statusStyle = 'bg-[#ff6b00] text-white';
                                } else {
                                    $statusDisplay = 'RECEIVED';
                                    $statusStyle = 'bg-[#357af6] text-white';
                                }
                            @endphp
                            <tr class="asset-row hover:bg-slate-50/80 transition-colors" 
                                data-barcode="{{ strtolower($item->barcode) }}" 
                                data-name="{{ strtolower($item->name ?: '') }}"
                                data-serials="{{ strtolower($item->serial_number ?: '') }}"
                                data-category="{{ strtolower($item->category ?: '') }}"
                                data-status="{{ strtolower($statusDisplay) }}"
                                data-date="{{ $item->created_at ? $item->created_at->format('Y-m-d') : '' }}">
                                <td class="px-4 py-4.5 text-center">
                                    <input type="checkbox" class="asset-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer" value="{{ $item->id }}" data-barcode="{{ $item->barcode }}">
                                </td>
                                <td class="px-6 py-4.5 whitespace-nowrap">
                                    <span class="font-extrabold text-slate-900 text-sm sm:text-base flex items-center gap-2 group cursor-pointer" 
                                          onclick="window.copyToClipboard('{{ $item->barcode }}', 'Barcode')">
                                        <i class="fa-solid fa-laptop text-slate-400 group-hover:text-indigo-600 transition-colors text-base"></i>
                                        {{ $item->barcode }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5">
                                    <div class="flex items-center gap-1.5 flex-wrap max-w-[250px]">
                                        <span class="font-extrabold text-slate-900 text-sm sm:text-base truncate max-w-[170px] inline-block" title="{{ $item->name ?: 'IT Asset' }}">
                                            {{ $item->primary_name }}
                                        </span>
                                        @if ($item->total_parsed_count > 1)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0" title="Total items in this entry: {{ $item->total_parsed_count }}">
                                                +{{ $item->total_parsed_count - 1 }} more
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4.5">
                                    <div class="flex items-center gap-1.5 flex-wrap max-w-[220px]">
                                        <span class="text-slate-700 font-bold text-xs sm:text-sm truncate max-w-[150px] inline-block" title="Brand: {{ $item->brand }} | Model: {{ $item->model }}">
                                            {{ $item->primary_brand }} / {{ $item->primary_model }}
                                        </span>
                                        @if ($item->total_parsed_count > 1)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-600 shrink-0" title="{{ $item->total_parsed_count }} models total">
                                                {{ $item->total_parsed_count }} models
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4.5 text-slate-600 text-xs sm:text-sm font-medium whitespace-nowrap">
                                    {{ $item->category ?: 'IT Assets' }}
                                </td>
                                <td class="px-6 py-4.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-3.5 py-1.5 rounded-full text-xs sm:text-[13px] font-black uppercase tracking-wider text-white shadow-2xs {{ $statusStyle }}">
                                        {{ $statusDisplay }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center text-sm sm:text-base whitespace-nowrap">
                                    <span class="text-slate-900 font-extrabold">{{ $item->quantity }}</span>
                                    <span class="text-slate-300 mx-1">|</span>
                                    <span class="text-slate-600 font-medium">{{ $item->weight ?: 0 }} <span class="text-xs text-slate-400 font-bold">lbs</span></span>
                                </td>
                                <td class="px-6 py-4.5">
                                    @if ($item->primary_serial)
                                        <div class="flex items-center gap-1.5 flex-wrap max-w-[200px]">
                                            <span class="font-mono text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg truncate max-w-[130px] inline-block" 
                                                  title="{{ $item->serial_number }}">
                                                {{ $item->primary_serial }}
                                            </span>
                                            @if ($item->total_parsed_count > 1)
                                                <span class="text-xs font-extrabold text-slate-500 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded" title="{{ $item->serial_number }}">
                                                    +{{ $item->total_parsed_count - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs sm:text-sm font-bold">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4.5 text-slate-600 text-xs sm:text-sm font-bold whitespace-nowrap">
                                    {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2 shrink-0">
                                        <a href="{{ route('user.it-assets.show', $item->id) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 whitespace-nowrap shrink-0">
                                            <i class="fa-solid fa-eye text-xs sm:text-sm"></i>
                                            View Details
                                        </a>
                                        <button type="button"
                                                onclick="openSingleDeleteModal({{ $item->id }}, '{{ addslashes($item->primary_name) }}')"
                                                class="inline-flex items-center justify-center h-8.5 w-8.5 rounded-xl border border-rose-200 bg-white text-rose-600 shadow-2xs transition hover:bg-rose-50 hover:border-rose-300 hover:text-rose-700 shrink-0"
                                                title="Delete Asset Item">
                                            <i class="fa-solid fa-trash-can text-xs sm:text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noAssetMatches" class="hidden">
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500 text-sm font-semibold">
                                No IT assets found matching your filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $items->links() }}
                </div>
            @endif
        @elseif ($noClientLinked ?? false)
            <div class="text-center py-16 px-6">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 text-2xl mb-4">
                    <i class="fa-solid fa-link-slash"></i>
                </span>
                <h3 class="text-base font-extrabold text-slate-900">No Client Profile Linked</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                    Your account (<span class="font-bold text-slate-700">{{ $userEmail }}</span>) is not linked to an organization profile in our system. Please contact support to pair your profile.
                </p>
                <a href="{{ Route::has('contact') ? route('contact') : url('/contact-us') }}" 
                   class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-5 py-2.5 text-xs font-bold hover:bg-slate-800 transition">
                    <i class="fa-solid fa-envelope"></i> Contact Support
                </a>
            </div>
        @else
            <div class="text-center py-16 px-6">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 text-2xl mb-4">
                    <i class="fa-solid fa-laptop"></i>
                </span>
                <h3 class="text-base font-extrabold text-slate-900">No IT Assets Recorded</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">There are currently no IT asset items recorded for your organization profile.</p>
            </div>
        @endif
    </section>
</div>

<!-- Single Delete Confirmation Modal -->
<div id="singleDeleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 transition-opacity duration-200">
    <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-100">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Delete IT Asset Entry</h3>
                <p class="text-xs text-slate-500 font-medium">This action cannot be undone.</p>
            </div>
        </div>

        <p class="text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
            Are you sure you want to delete IT Asset entry <span id="singleDeleteItemName" class="font-extrabold text-slate-900"></span>?
        </p>

        <form id="singleDeleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeSingleDeleteModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 text-xs font-bold text-white hover:bg-rose-700 shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can"></i> Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 transition-opacity duration-200">
    <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-100">
                <i class="fa-solid fa-trash-can text-xl"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Delete Selected IT Assets</h3>
                <p class="text-xs text-slate-500 font-medium">Permanent deletion of selected asset records.</p>
            </div>
        </div>

        <p class="text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
            Are you sure you want to delete <span id="bulkDeleteModalCount" class="font-extrabold text-slate-900">0</span> selected IT Asset record(s)?
        </p>

        <form id="bulkDeleteForm" method="POST" action="{{ route('user.it-assets.bulk-delete') }}">
            @csrf
            <div id="bulkDeleteInputsContainer"></div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeBulkDeleteModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 text-xs font-bold text-white hover:bg-rose-700 shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can"></i> Yes, Delete All Selected
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('assetSearchInput');
    const statusSelect = document.getElementById('assetStatusFilter');
    const dateInput = document.getElementById('assetDateFilter');
    const resetBtn = document.getElementById('resetAssetFiltersBtn');
    const rows = document.querySelectorAll('.asset-row');
    const noMatches = document.getElementById('noAssetMatches');

    // Checkbox & Bulk Actions
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    const bulkActionToolbar = document.getElementById('bulkActionToolbar');
    const selectedCountText = document.getElementById('selectedCountText');
    const bulkDeleteBtnCount = document.getElementById('bulkDeleteBtnCount');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.asset-checkbox:checked');
        const count = checked.length;

        if (selectedCountText) selectedCountText.textContent = `${count} item${count === 1 ? '' : 's'} selected`;
        if (bulkDeleteBtnCount) bulkDeleteBtnCount.textContent = count;

        if (bulkActionToolbar) {
            bulkActionToolbar.classList.toggle('hidden', count === 0);
        }

        if (selectAllCheckbox) {
            const visibleCheckboxes = Array.from(document.querySelectorAll('.asset-row:not(.hidden) .asset-checkbox'));
            if (visibleCheckboxes.length > 0) {
                selectAllCheckbox.checked = visibleCheckboxes.every(cb => cb.checked);
            } else {
                selectAllCheckbox.checked = false;
            }
        }
    }

    selectAllCheckbox?.addEventListener('change', (e) => {
        const isChecked = e.target.checked;
        document.querySelectorAll('.asset-row:not(.hidden) .asset-checkbox').forEach(cb => {
            cb.checked = isChecked;
        });
        updateBulkToolbar();
    });

    assetCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkToolbar);
    });

    deselectAllBtn?.addEventListener('click', () => {
        assetCheckboxes.forEach(cb => cb.checked = false);
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkToolbar();
    });

    function openSingleDeleteModal(id, name) {
        const modal = document.getElementById('singleDeleteModal');
        const form = document.getElementById('singleDeleteForm');
        const itemNameSpan = document.getElementById('singleDeleteItemName');

        if (form) {
            form.action = `/user-it-assets/${id}`;
        }
        if (itemNameSpan) {
            itemNameSpan.textContent = `"${name}"`;
        }
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeSingleDeleteModal() {
        const modal = document.getElementById('singleDeleteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    bulkDeleteBtn?.addEventListener('click', () => {
        const checked = document.querySelectorAll('.asset-checkbox:checked');
        if (checked.length === 0) return;

        const modal = document.getElementById('bulkDeleteModal');
        const modalCount = document.getElementById('bulkDeleteModalCount');
        const container = document.getElementById('bulkDeleteInputsContainer');

        if (modalCount) modalCount.textContent = checked.length;
        if (container) {
            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    });

    function closeBulkDeleteModal() {
        const modal = document.getElementById('bulkDeleteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function filterAssets() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const selectedStatus = (statusSelect?.value || 'all').toLowerCase();
        const selectedDate = dateInput?.value || '';
        let visibleCount = 0;

        rows.forEach(row => {
            const barcode = row.dataset.barcode || '';
            const name = row.dataset.name || '';
            const serials = row.dataset.serials || '';
            const category = row.dataset.category || '';
            const status = (row.dataset.status || '').toLowerCase();
            const date = row.dataset.date || '';

            const matchesQuery = !query || barcode.includes(query) || name.includes(query) || serials.includes(query) || category.includes(query);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;
            const matchesDate = !selectedDate || date === selectedDate;

            if (matchesQuery && matchesStatus && matchesDate) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        if (noMatches) {
            noMatches.classList.toggle('hidden', visibleCount > 0);
        }

        updateBulkToolbar();
    }

    searchInput?.addEventListener('input', filterAssets);
    statusSelect?.addEventListener('change', filterAssets);
    dateInput?.addEventListener('change', filterAssets);

    resetBtn?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = 'all';
        if (dateInput) dateInput.value = '';
        filterAssets();
    });

    // Drag-to-scroll (Hand cursor scroll) for table wrapper
    const scrollWrapper = document.getElementById('tableScrollWrapper');
    if (scrollWrapper) {
        let isDown = false;
        let startX;
        let scrollLeft;

        scrollWrapper.addEventListener('mousedown', (e) => {
            if (['INPUT', 'BUTTON', 'A', 'I', 'SELECT', 'LABEL'].includes(e.target.tagName) || e.target.closest('button, a, input, select')) {
                return;
            }
            isDown = true;
            scrollWrapper.classList.add('cursor-grabbing');
            startX = e.pageX - scrollWrapper.offsetLeft;
            scrollLeft = scrollWrapper.scrollLeft;
        });

        scrollWrapper.addEventListener('mouseleave', () => {
            isDown = false;
            scrollWrapper.classList.remove('cursor-grabbing');
        });

        scrollWrapper.addEventListener('mouseup', () => {
            isDown = false;
            scrollWrapper.classList.remove('cursor-grabbing');
        });

        scrollWrapper.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollWrapper.offsetLeft;
            const walk = (x - startX) * 2;
            scrollWrapper.scrollLeft = scrollLeft - walk;
        });
    }
</script>
@endpush
@endsection

