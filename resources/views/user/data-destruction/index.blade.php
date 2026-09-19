@extends('user.layouts.app')

@section('title', 'Data Destruction')

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-8">
    {{-- Header Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-2xl sm:px-8 lg:px-10 lg:py-10">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-rose-600/30 to-violet-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-indigo-500/20 to-red-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-rose-500/10 border border-rose-400/30 text-xs font-bold uppercase tracking-widest text-rose-300">
                        <i class="fa-solid fa-shield-halved mr-1.5 text-rose-400"></i> Security & Compliance
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Linked: {{ $client->name }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    Data Destruction
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Track the serialization, DoD/NIST compliant wiping progress, and certificates of data destruction for your storage media.
                </p>
            </div>
        </div>
    </section>

    {{-- Stats Cards Grid --}}
    @if ($items->count() > 0)
        @php
            $wipedTotal = $items->sum('reuse_quantity');
            $scrappedTotal = $items->sum('scrap_quantity');
            $grandTotal = $items->sum('quantity');
        @endphp
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Logged Media Entries</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900">{{ $items->total() }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-100">
                        <i class="fa-solid fa-hard-drive text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Sanitized Devices</p>
                        <p class="mt-1.5 text-2xl font-black text-emerald-600">{{ number_format($wipedTotal) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </span>
                </div>
            </div>

            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Shredded / Scrapped</p>
                        <p class="mt-1.5 text-2xl font-black text-slate-900">{{ number_format($scrappedTotal) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 border border-slate-200">
                        <i class="fa-solid fa-shredder text-lg"></i>
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
                       id="destructionSearchInput" 
                       placeholder="Search barcode, device name, serial..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 placeholder-slate-400 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition">
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select id="destructionStatusFilter" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition w-full sm:w-auto">
                    <option value="all">All Statuses</option>
                    <option value="received">RECEIVED</option>
                    <option value="processing">PROCESSING</option>
                    <option value="completed">COMPLETED</option>
                </select>
            </div>
        </div>

        @if ($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[950px]">
                    <thead>
                        <tr class="text-[10px] uppercase font-black text-slate-400 tracking-wider bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4">Barcode #</th>
                            <th class="px-6 py-4">Device Name</th>
                            <th class="px-6 py-4">Brand / Model</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Qty / Weight</th>
                            <th class="px-6 py-4">Logged Serials</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="destructionTableBody" class="text-xs font-bold text-slate-700 divide-y divide-slate-100">
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
                            <tr class="destruction-row hover:bg-slate-50/80 transition-colors" 
                                data-barcode="{{ strtolower($item->barcode) }}" 
                                data-name="{{ strtolower($item->name) }}"
                                data-serials="{{ strtolower($item->serial_number ?: '') }}"
                                data-status="{{ strtolower($statusDisplay) }}">
                                <td class="px-6 py-4.5">
                                    <span class="font-extrabold text-slate-900 flex items-center gap-2 group cursor-pointer" 
                                          onclick="window.copyToClipboard('{{ $item->barcode }}', 'Barcode')">
                                        <i class="fa-solid fa-hard-drive text-slate-400 group-hover:text-rose-600 transition-colors"></i>
                                        {{ $item->barcode }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5">
                                    <div class="flex items-center gap-1.5 flex-wrap max-w-[250px]">
                                        <span class="font-extrabold text-slate-900 truncate max-w-[170px] inline-block" title="{{ $item->name }}">
                                            {{ $item->primary_name }}
                                        </span>
                                        @if ($item->total_parsed_count > 1)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 shrink-0" title="Total media items in this entry: {{ $item->total_parsed_count }}">
                                                +{{ $item->total_parsed_count - 1 }} more
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4.5">
                                    <div class="flex items-center gap-1.5 flex-wrap max-w-[220px]">
                                        <span class="text-slate-700 font-bold truncate max-w-[150px] inline-block" title="Brand: {{ $item->brand }} | Model: {{ $item->model }}">
                                            {{ $item->primary_brand }} / {{ $item->primary_model }}
                                        </span>
                                        @if ($item->total_parsed_count > 1)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-slate-100 text-slate-600 shrink-0" title="{{ $item->total_parsed_count }} models total">
                                                {{ $item->total_parsed_count }} models
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4.5 text-slate-500 font-medium">
                                    {{ $item->category }}
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="inline-block px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider text-white shadow-2xs {{ $statusStyle }}">
                                        {{ $statusDisplay }}
                                    </span>
                                </td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="text-slate-900 font-extrabold">{{ $item->quantity }}</span>
                                    <span class="text-slate-300 mx-1">|</span>
                                    <span class="text-slate-500 font-medium">{{ $item->weight ?: 0 }} lbs</span>
                                </td>
                                <td class="px-6 py-4.5">
                                    @if ($item->primary_serial)
                                        <div class="flex items-center gap-1.5 flex-wrap max-w-[200px]">
                                            <span class="font-mono text-[11px] font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg truncate max-w-[130px] inline-block" 
                                                  title="{{ $item->serial_number }}">
                                                {{ $item->primary_serial }}
                                            </span>
                                            @if ($item->total_parsed_count > 1)
                                                <span class="text-[10px] font-extrabold text-slate-500 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded" title="{{ $item->serial_number }}">
                                                    +{{ $item->total_parsed_count - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px]">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4.5 text-right">
                                    <a href="{{ route('user.data-destruction.show', $item->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600">
                                        <i class="fa-solid fa-shield-halved text-xs"></i>
                                        Compliance Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noDestructionMatches" class="hidden">
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 font-semibold">
                                No data destruction items found matching your filter.
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
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <h3 class="text-base font-extrabold text-slate-900">No Data Destruction Items Recorded</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">There are currently no serialized data destruction items recorded for your organization profile.</p>
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('destructionSearchInput');
    const statusSelect = document.getElementById('destructionStatusFilter');
    const rows = document.querySelectorAll('.destruction-row');
    const noMatches = document.getElementById('noDestructionMatches');

    function filterDestruction() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const selectedStatus = (statusSelect?.value || 'all').toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const barcode = row.dataset.barcode || '';
            const name = row.dataset.name || '';
            const serials = row.dataset.serials || '';
            const status = (row.dataset.status || '').toLowerCase();

            const matchesQuery = !query || barcode.includes(query) || name.includes(query) || serials.includes(query);
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

    searchInput?.addEventListener('input', filterDestruction);
    statusSelect?.addEventListener('change', filterDestruction);
</script>
@endpush
@endsection

