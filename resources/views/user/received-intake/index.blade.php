@extends('user.layouts.app')

@section('title', 'Received Intake')

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
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-blue-600/30 to-cyan-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-indigo-500/20 to-teal-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-xs font-bold uppercase tracking-widest text-blue-300">
                        <i class="fa-solid fa-truck-ramp-box mr-1.5 text-blue-400"></i> IT Asset & E-waste Tracking
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Linked: {{ $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    Receiving / Processing
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Track processing status, remaining inventory counts, and net weight breakdown of all shipment pallets received from your facility.
                </p>
            </div>
        </div>
    </section>

    {{-- Stats Summary Grid --}}
    @if ($stats)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Card 1: Recieved item / Pallet -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Recieved item / Pallet</p>
                        <p class="mt-1.5 text-2xl font-black text-blue-600">
                            {{ $stats['receivedPalletsCount'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </span>
                </div>
            </div>

            <!-- Card 2: Processing item / Pallet -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Processing item / Pallet</p>
                        <p class="mt-1.5 text-2xl font-black text-amber-600">
                            {{ $stats['processingPalletsCount'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-100">
                        <i class="fa-solid fa-gears text-lg"></i>
                    </span>
                </div>
            </div>

            <!-- Card 3: Completed item / Pallet -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Completed item / Pallet</p>
                        <p class="mt-1.5 text-2xl font-black text-emerald-600">
                            {{ $stats['completedPalletsCount'] ?? 0 }}
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
                       id="palletSearchInput" 
                       placeholder="Search barcode, location, description..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <!-- Status Filter Dropdown -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="statusFilterSelect" class="text-sm font-bold text-slate-600 whitespace-nowrap">Status:</label>
                    <select id="statusFilterSelect" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition w-full sm:w-auto">
                        <option value="all">All Statuses</option>
                        <option value="received">Received</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Date Filter Input -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="dateFilterInput" class="text-sm font-bold text-slate-600 whitespace-nowrap">Received Date:</label>
                    <input type="date" 
                           id="dateFilterInput" 
                           class="px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition w-full sm:w-auto">
                </div>

                <!-- Reset Filters Button -->
                <button type="button" 
                        id="resetFiltersBtn"
                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 rounded-xl text-sm font-bold transition flex items-center gap-1.5"
                        title="Clear all filters">
                    <i class="fa-solid fa-arrow-rotate-left text-xs"></i> Reset
                </button>
            </div>
        </div>

        <!-- Bulk Action Floating / Sticky Toolbar -->
        <div id="bulkActionToolbar" class="hidden border-b border-blue-100 bg-blue-50/90 px-6 py-3.5 flex flex-wrap items-center justify-between gap-4 transition-all">
            <div class="flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-600 text-white font-extrabold text-xs">
                    <i class="fa-solid fa-check-double text-xs"></i>
                </span>
                <span id="selectedCountText" class="text-sm font-extrabold text-blue-950">0 items selected</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="deselectAllBtn" class="px-3.5 py-1.5 rounded-xl border border-blue-200 bg-white hover:bg-slate-50 text-sm font-bold text-slate-700 transition shadow-2xs">
                    Deselect All
                </button>
                <button type="button" id="bulkDeleteBtn" class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-sm font-bold text-white transition shadow-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i> Delete Selected (<span id="bulkDeleteBtnCount">0</span>)
                </button>
            </div>
        </div>

        @if ($pallets->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1050px]">
                    <thead>
                        <tr class="text-xs sm:text-[13px] uppercase font-black text-slate-500 tracking-wider bg-slate-50/70 border-b border-slate-200">
                            <th class="px-4 py-4 w-10 text-center">
                                <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer" title="Select All Visible">
                            </th>
                            <th class="px-6 py-4">Pallet Barcode #</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Items (Total / Left)</th>
                            <th class="px-6 py-4 text-center">Net Weight (Total / Left)</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4">Received Date</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="palletTableBody" class="text-sm font-bold text-slate-800 divide-y divide-slate-100">
                        @foreach ($pallets as $pallet)
                            @php
                                $statusStyle = match (strtolower(trim($pallet->status ?: 'received'))) {
                                    'received', 'pending' => 'bg-[#357af6] text-white',
                                    'processing', 'in processing' => 'bg-[#ff6b00] text-white',
                                    'completed' => 'bg-[#10b981] text-white',
                                    default => 'bg-[#357af6] text-white',
                                };
                            @endphp
                            <tr class="pallet-row hover:bg-slate-50/80 transition-colors" 
                                data-barcode="{{ strtolower(($pallet->display_barcode ?? $pallet->barcode_number) . ' ' . $pallet->barcode_number) }}" 
                                data-status="{{ strtolower($pallet->status ?: 'received') }}"
                                data-location="{{ strtolower($pallet->put_away_location ?: '') }}"
                                data-description="{{ strtolower($pallet->description ?: '') }}"
                                data-date="{{ $pallet->created_at ? $pallet->created_at->format('Y-m-d') : '' }}">
                                <td class="px-4 py-4.5 text-center">
                                    <input type="checkbox" class="pallet-checkbox w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer" value="{{ $pallet->id }}" data-barcode="{{ $pallet->display_barcode ?? $pallet->barcode_number }}">
                                </td>
                                <td class="px-6 py-4.5">
                                    <span class="font-extrabold text-slate-900 text-sm sm:text-base flex items-center gap-2 group cursor-pointer" 
                                          onclick="window.copyToClipboard('{{ $pallet->barcode_number }}', 'Barcode')">
                                        <i class="fa-solid fa-barcode text-slate-400 group-hover:text-blue-600 transition-colors text-base"></i>
                                        {{ $pallet->display_barcode ?? $pallet->barcode_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="inline-block px-4 py-1.5 rounded-full text-xs sm:text-[13px] font-black uppercase tracking-wider text-white shadow-xs {{ $statusStyle }}">
                                        {{ $pallet->status ?: 'Received' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center text-sm sm:text-base">
                                    <span class="text-slate-900 font-extrabold">{{ $pallet->estimated_count }}</span>
                                    <span class="text-slate-300 mx-1">/</span>
                                    <span class="text-blue-600 font-extrabold">{{ $pallet->remaining_quantity }}</span>
                                </td>
                                <td class="px-6 py-4.5 text-center text-sm sm:text-base">
                                    <span class="text-slate-900 font-extrabold">{{ number_format($pallet->gross_weight - $pallet->tare_weight, 1) }} <span class="text-xs text-slate-400 font-bold">lbs</span></span>
                                    <span class="text-slate-300 mx-1">/</span>
                                    <span class="text-emerald-600 font-extrabold">{{ number_format($pallet->remaining_weight, 1) }} <span class="text-xs text-slate-400 font-bold">lbs</span></span>
                                </td>
                                <td class="px-6 py-4.5">
                                    @if ($pallet->put_away_location)
                                        <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-800 bg-slate-100 border border-slate-200 px-3 py-1 rounded-lg">
                                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                                            {{ $pallet->put_away_location }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs sm:text-sm font-bold">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4.5">
                                    <p class="max-w-[240px] truncate text-slate-600 text-xs sm:text-sm font-medium" title="{{ $pallet->description }}">
                                        {{ $pallet->description ?: 'No description' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4.5 text-slate-600 text-xs sm:text-sm font-bold">
                                    {{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4.5 text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a href="{{ route('user.received-intake.show', $pallet->id) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs sm:text-sm font-bold text-slate-700 shadow-2xs transition hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600">
                                            <i class="fa-solid fa-eye text-xs sm:text-sm"></i>
                                            View
                                        </a>
                                        <button type="button"
                                                onclick="openSingleDeleteModal({{ $pallet->id }}, '{{ $pallet->barcode_number }}')"
                                                class="inline-flex items-center justify-center h-8.5 w-8.5 rounded-xl border border-rose-200 bg-white text-rose-600 shadow-2xs transition hover:bg-rose-50 hover:border-rose-300 hover:text-rose-700"
                                                title="Delete Pallet">
                                            <i class="fa-solid fa-trash-can text-xs sm:text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noPalletMatches" class="hidden">
                            <td colspan="9" class="px-6 py-12 text-center text-slate-500 text-sm font-semibold">
                                No intake pallets found matching your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($pallets->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $pallets->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16 px-6">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 text-2xl mb-4">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
                <h3 class="text-base font-extrabold text-slate-900">No received intake pallets recorded</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">There are currently no received shipment pallets registered for your organization profile.</p>
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
                <h3 class="text-base font-extrabold text-slate-900">Delete Intake Pallet</h3>
                <p class="text-xs text-slate-500 font-medium">This action cannot be undone.</p>
            </div>
        </div>

        <p class="text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
            Are you sure you want to delete intake pallet <span id="singleDeleteBarcode" class="font-extrabold text-slate-900">#</span>?
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
                <h3 class="text-base font-extrabold text-slate-900">Delete Selected Pallets</h3>
                <p class="text-xs text-slate-500 font-medium">Permanent deletion of selected items.</p>
            </div>
        </div>

        <p class="text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
            Are you sure you want to delete <span id="bulkDeleteModalCount" class="font-extrabold text-slate-900">0</span> selected intake pallet(s)?
        </p>

        <form id="bulkDeleteForm" method="POST" action="{{ route('user.received-intake.bulk-delete') }}">
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
    const searchInput = document.getElementById('palletSearchInput');
    const statusSelect = document.getElementById('statusFilterSelect');
    const dateInput = document.getElementById('dateFilterInput');
    const resetBtn = document.getElementById('resetFiltersBtn');
    const rows = document.querySelectorAll('.pallet-row');
    const noMatches = document.getElementById('noPalletMatches');

    // Checkbox & Bulk Actions
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const palletCheckboxes = document.querySelectorAll('.pallet-checkbox');
    const bulkActionToolbar = document.getElementById('bulkActionToolbar');
    const selectedCountText = document.getElementById('selectedCountText');
    const bulkDeleteBtnCount = document.getElementById('bulkDeleteBtnCount');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.pallet-checkbox:checked');
        const count = checked.length;

        if (selectedCountText) selectedCountText.textContent = `${count} item${count === 1 ? '' : 's'} selected`;
        if (bulkDeleteBtnCount) bulkDeleteBtnCount.textContent = count;

        if (bulkActionToolbar) {
            bulkActionToolbar.classList.toggle('hidden', count === 0);
        }

        if (selectAllCheckbox) {
            const visibleCheckboxes = Array.from(document.querySelectorAll('.pallet-row:not(.hidden) .pallet-checkbox'));
            if (visibleCheckboxes.length > 0) {
                selectAllCheckbox.checked = visibleCheckboxes.every(cb => cb.checked);
            } else {
                selectAllCheckbox.checked = false;
            }
        }
    }

    selectAllCheckbox?.addEventListener('change', (e) => {
        const isChecked = e.target.checked;
        document.querySelectorAll('.pallet-row:not(.hidden) .pallet-checkbox').forEach(cb => {
            cb.checked = isChecked;
        });
        updateBulkToolbar();
    });

    palletCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkToolbar);
    });

    deselectAllBtn?.addEventListener('click', () => {
        palletCheckboxes.forEach(cb => cb.checked = false);
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkToolbar();
    });

    function openSingleDeleteModal(id, barcode) {
        const modal = document.getElementById('singleDeleteModal');
        const form = document.getElementById('singleDeleteForm');
        const barcodeSpan = document.getElementById('singleDeleteBarcode');

        if (form) {
            form.action = `/user-received-intake/${id}`;
        }
        if (barcodeSpan) {
            barcodeSpan.textContent = `#${barcode}`;
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
        const checked = document.querySelectorAll('.pallet-checkbox:checked');
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

    function filterPallets() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const selectedStatus = (statusSelect?.value || 'all').toLowerCase();
        const selectedDate = dateInput?.value || '';
        let visibleCount = 0;

        rows.forEach(row => {
            const barcode = row.dataset.barcode || '';
            const location = row.dataset.location || '';
            const description = row.dataset.description || '';
            const status = (row.dataset.status || '').toLowerCase();
            const date = row.dataset.date || '';

            const matchesQuery = !query || barcode.includes(query) || location.includes(query) || description.includes(query);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;
            const matchesDate = !selectedDate || date === selectedDate;

            if (matchesQuery && matchesStatus && matchesDate) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
                // Deselect hidden row checkbox when filtered out if desired, or keep as is
            }
        });

        if (noMatches) {
            noMatches.classList.toggle('hidden', visibleCount > 0);
        }

        updateBulkToolbar();
    }

    searchInput?.addEventListener('input', filterPallets);
    statusSelect?.addEventListener('change', filterPallets);
    dateInput?.addEventListener('change', filterPallets);

    resetBtn?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = 'all';
        if (dateInput) dateInput.value = '';
        filterPallets();
    });
</script>
@endpush
@endsection
