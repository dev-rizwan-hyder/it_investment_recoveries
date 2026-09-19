@extends('user.layouts.app')

@section('title', 'Received Intake')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Header Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-blue-600/30 to-cyan-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-indigo-500/20 to-teal-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-xs font-bold uppercase tracking-widest text-blue-300">
                        <i class="fa-solid fa-truck-ramp-box mr-1.5 text-blue-400"></i> Recycling Tracking
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Linked: {{ $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    Received Intakes
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Track processing status, remaining inventory counts, and net weight breakdown of all shipment pallets received from your facility.
                </p>
            </div>
        </div>
    </section>

    {{-- Stats Summary Grid --}}
    @if ($stats)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Pallets</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900">{{ $stats['totalPallets'] ?? 0 }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">In Processing</p>
                        <p class="mt-1.5 text-2xl font-black text-blue-600">{{ $stats['processingCount'] ?? 0 }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 border border-sky-100">
                        <i class="fa-solid fa-gears text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Remaining Items</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900">{{ number_format($stats['totalItems'] ?? 0) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                        <i class="fa-solid fa-cubes text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Remaining Weight</p>
                        <p class="mt-1.5 text-2xl font-black text-emerald-600">{{ number_format($stats['totalWeight'] ?? 0, 1) }} <span class="text-xs text-slate-400">lbs</span></p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fa-solid fa-weight-hanging text-lg"></i>
                    </span>
                </div>
            </div>
        </section>
    @endif

    {{-- Main Table Section --}}
    <section class="portal-card rounded-3xl overflow-hidden">
        <!-- Search & Filter Bar -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       id="palletSearchInput" 
                       placeholder="Search barcode, location..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 placeholder-slate-400 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select id="statusFilterSelect" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition w-full sm:w-auto">
                    <option value="all">All Statuses</option>
                    <option value="received">Received</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        @if ($pallets->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="text-[10px] uppercase font-black text-slate-400 tracking-wider bg-slate-50/50 border-b border-slate-100">
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
                    <tbody id="palletTableBody" class="text-xs font-bold text-slate-700 divide-y divide-slate-100">
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
                                data-barcode="{{ strtolower($pallet->barcode_number) }}" 
                                data-status="{{ strtolower($pallet->status ?: 'received') }}"
                                data-location="{{ strtolower($pallet->put_away_location ?: '') }}">
                                <td class="px-6 py-4.5">
                                    <span class="font-extrabold text-slate-900 flex items-center gap-2 group cursor-pointer" 
                                          onclick="window.copyToClipboard('{{ $pallet->barcode_number }}', 'Barcode')">
                                        <i class="fa-solid fa-barcode text-slate-400 group-hover:text-blue-600 transition-colors"></i>
                                        {{ $pallet->barcode_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white shadow-xs {{ $statusStyle }}">
                                        {{ $pallet->status ?: 'Received' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="text-slate-900 font-extrabold">{{ $pallet->estimated_count }}</span>
                                    <span class="text-slate-300 mx-1">/</span>
                                    <span class="text-blue-600 font-extrabold">{{ $pallet->remaining_quantity }}</span>
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="text-slate-900 font-extrabold">{{ number_format($pallet->gross_weight - $pallet->tare_weight, 1) }} <span class="text-[10px] text-slate-400">lbs</span></span>
                                    <span class="text-slate-300 mx-1">/</span>
                                    <span class="text-emerald-600 font-extrabold">{{ number_format($pallet->remaining_weight, 1) }} <span class="text-[10px] text-slate-400">lbs</span></span>
                                </td>
                                <td class="px-6 py-4.5">
                                    @if ($pallet->put_away_location)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg">
                                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                                            {{ $pallet->put_away_location }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4.5">
                                    <p class="max-w-[220px] truncate text-slate-500 font-medium" title="{{ $pallet->description }}">
                                        {{ $pallet->description ?: 'No description' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4.5 text-slate-500 font-medium">
                                    {{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4.5 text-right">
                                    <a href="{{ route('user.received-intake.show', $pallet->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        View Items
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noPalletMatches" class="hidden">
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 font-semibold">
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

@push('scripts')
<script>
    const searchInput = document.getElementById('palletSearchInput');
    const statusSelect = document.getElementById('statusFilterSelect');
    const rows = document.querySelectorAll('.pallet-row');
    const noMatches = document.getElementById('noPalletMatches');

    function filterPallets() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const selectedStatus = statusSelect?.value || 'all';
        let visibleCount = 0;

        rows.forEach(row => {
            const barcode = row.dataset.barcode || '';
            const location = row.dataset.location || '';
            const status = row.dataset.status || '';

            const matchesQuery = !query || barcode.includes(query) || location.includes(query);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;

            if (matchesQuery && matchesStatus) {
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

    searchInput?.addEventListener('input', filterPallets);
    statusSelect?.addEventListener('change', filterPallets);
</script>
@endpush
@endsection

