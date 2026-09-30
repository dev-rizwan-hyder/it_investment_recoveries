@extends('user.layouts.app')

@section('title', 'Universal Waste / Certificate')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Header Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-emerald-600/30 to-teal-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-green-500/20 to-lime-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold uppercase tracking-widest text-emerald-300">
                        <i class="fa-solid fa-recycle mr-1.5 text-emerald-400"></i> Environmental Recycling & Compliance
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Linked: {{ $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    Universal Waste & Recycling Certificates
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Monitor EPA-compliant universal waste recycling, zero-landfill processing, and official Certificates of Recycling for your electronic hardware.
                </p>
            </div>
        </div>
    </section>

    {{-- Stats Cards Grid --}}
    @if (($items->count() ?? 0) > 0)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Items Processed</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900">{{ number_format($stats['totalItems'] ?? 0) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Recycled Weight</p>
                        <p class="mt-1.5 text-2xl font-black text-emerald-600">{{ number_format($stats['totalWeight'] ?? 0, 1) }} lbs</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 border border-teal-100">
                        <i class="fa-solid fa-weight-hanging text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Recycled & Completed</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900">{{ number_format($stats['completedCount'] ?? 0) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-green-50 text-green-600 border border-green-100">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Certificates Issued</p>
                        <p class="mt-1.5 text-2xl font-black text-indigo-600">{{ number_format($stats['certificatesCount'] ?? 0) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                        <i class="fa-solid fa-file-certificate text-lg"></i>
                    </span>
                </div>
            </div>
        </section>
    @endif

    {{-- Main Table Section --}}
    <section class="portal-card rounded-3xl overflow-hidden">
        <!-- Search & Filter Bar -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col lg:flex-row gap-4 justify-between items-center">
            <div class="relative w-full lg:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       id="wasteSearchInput" 
                       placeholder="Search barcode, item name, serial..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition">
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="wasteStatusFilter" class="text-xs font-bold text-slate-500 whitespace-nowrap">Status:</label>
                    <select id="wasteStatusFilter" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition w-full sm:w-auto">
                        <option value="all">All Statuses</option>
                        <option value="received">RECEIVED</option>
                        <option value="processing">PROCESSING</option>
                        <option value="completed">COMPLETED</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="wasteDateFilter" class="text-xs font-bold text-slate-500 whitespace-nowrap">Date:</label>
                    <input type="date" 
                           id="wasteDateFilter" 
                           class="px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition w-full sm:w-auto">
                </div>

                <button type="button" 
                        id="resetWasteFiltersBtn"
                        class="px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                        title="Clear all filters">
                    <i class="fa-solid fa-arrow-rotate-left text-[10px]"></i> Reset
                </button>
            </div>
        </div>

        @if (($items->count() ?? 0) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="text-[10px] uppercase font-black text-slate-400 tracking-wider bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4">Pallet Barcode</th>
                            <th class="px-6 py-4">Item Name</th>
                            <th class="px-6 py-4">Brand / Model</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Qty / Weight</th>
                            <th class="px-6 py-4">Certificate Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="wasteTableBody" class="text-xs font-bold text-slate-700 divide-y divide-slate-100">
                        @foreach ($items as $item)
                            @php
                                $rawStatus = strtolower(trim($item->status ?? ''));
                                if (str_contains($rawStatus, 'complet') || str_contains($rawStatus, 'recycl') || str_contains($rawStatus, 'ready')) {
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
                            <tr class="waste-row hover:bg-slate-50/80 transition-colors" 
                                data-barcode="{{ strtolower($item->pallet_number ?? $item->barcode ?? '') }}" 
                                data-name="{{ strtolower($item->name ?? '') }}"
                                data-serials="{{ strtolower($item->serial_number ?? '') }}"
                                data-status="{{ strtolower($statusDisplay) }}">
                                <td class="px-6 py-4.5">
                                    <span class="font-extrabold text-slate-900 flex items-center gap-2 group cursor-pointer" 
                                          onclick="window.copyToClipboard('{{ $item->pallet_number ?? $item->barcode }}', 'Pallet Barcode')">
                                        <i class="fa-solid fa-recycle text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                                        {{ $item->pallet_number ?? $item->barcode ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5">
                                    <span class="font-extrabold text-slate-900 truncate max-w-[200px] inline-block" title="{{ $item->name ?? 'Universal Waste Item' }}">
                                        {{ $item->name ?? 'Universal Waste Item' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5">
                                    <span class="text-slate-700 font-bold truncate max-w-[170px] inline-block">
                                        {{ $item->brand ?? 'N/A' }} / {{ $item->model ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-slate-500 font-medium">
                                    {{ $item->category ?? 'Universal Waste' }}
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="inline-block px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider text-white shadow-2xs {{ $statusStyle }}">
                                        {{ $statusDisplay }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="text-slate-900 font-extrabold">{{ $item->quantity ?? 1 }}</span>
                                    <span class="text-slate-300 mx-1">|</span>
                                    <span class="text-slate-500 font-medium">{{ $item->weight ?: 0 }} lbs</span>
                                </td>
                                <td class="px-6 py-4.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Certificate Active
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-right">
                                    <a href="{{ route('user.universal-waste.show', $item->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600">
                                        <i class="fa-solid fa-file-contract text-xs"></i>
                                        View Certificate
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noWasteMatches" class="hidden">
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 font-semibold">
                                No universal waste items found matching your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if (method_exists($items, 'hasPages') && $items->hasPages())
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
                    <i class="fa-solid fa-recycle"></i>
                </span>
                <h3 class="text-base font-extrabold text-slate-900">No Universal Waste Items Recorded</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">There are currently no universal waste recycling items recorded for your organization profile.</p>
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('wasteSearchInput');
    const statusSelect = document.getElementById('wasteStatusFilter');
    const dateInput = document.getElementById('wasteDateFilter');
    const resetBtn = document.getElementById('resetWasteFiltersBtn');
    const rows = document.querySelectorAll('.waste-row');
    const noMatches = document.getElementById('noWasteMatches');

    function filterWaste() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const selectedStatus = (statusSelect?.value || 'all').toLowerCase();
        const selectedDate = dateInput?.value || '';
        let visibleCount = 0;

        rows.forEach(row => {
            const barcode = row.dataset.barcode || '';
            const name = row.dataset.name || '';
            const serials = row.dataset.serials || '';
            const status = (row.dataset.status || '').toLowerCase();
            const date = row.dataset.date || '';

            const matchesQuery = !query || barcode.includes(query) || name.includes(query) || serials.includes(query);
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
    }

    searchInput?.addEventListener('input', filterWaste);
    statusSelect?.addEventListener('change', filterWaste);
    dateInput?.addEventListener('change', filterWaste);

    resetBtn?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = 'all';
        if (dateInput) dateInput.value = '';
        filterWaste();
    });
</script>
@endpush
@endsection
