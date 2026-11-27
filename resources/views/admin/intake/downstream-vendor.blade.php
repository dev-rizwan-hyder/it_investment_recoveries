@extends('layouts.admin')

@section('content')
<div class="w-full animate-in fade-in duration-500 overflow-hidden relative">

    {{-- Page Heading --}}
    <div class="mb-6 p-2 md:p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                <i class="fas fa-truck text-blue-600"></i> Downstream Vendor
            </h1>
            <p class="text-slate-500 mt-1 text-sm">All shipment intake records submitted from Shipment Intake</p>
        </div>
        <a href="{{ route('admin.shipment-intake.index') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center gap-2 shadow-lg transition-all active:scale-95">
            <i class="fas fa-plus"></i> New Shipment Intake
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle mr-2 text-base"></i> {{ session('message') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-6" id="dv-stats-container"></div>

    {{-- Action Toolbar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <button id="dv-export-csv-btn"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                <i class="fas fa-file-export mr-2"></i> Export CSV
            </button>

            {{-- Bulk actions (hidden until selection) --}}
            <div id="dv-bulk-actions" class="hidden flex-wrap items-center gap-2 ml-auto">
                <span id="dv-bulk-count" class="text-xs font-black uppercase tracking-widest text-slate-500">0 selected</span>
                <select id="dv-bulk-status-select"
                    class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[170px]">
                    <option value="">Change status...</option>
                    <option value="Received">Received</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                </select>
                <button id="dv-bulk-status-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                    <i class="fas fa-sync-alt mr-2"></i> Change Status
                </button>
                <button id="dv-bulk-delete-btn"
                    class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                    <i class="fas fa-trash mr-2"></i> Delete Selected
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-50">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="dv-search" type="text" placeholder="Search shipments..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/10">
            </div>
            <select id="dv-status-filter"
                class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none">
                <option value="">All Statuses</option>
                <option value="Received">Received</option>
                <option value="Processing">Processing</option>
                <option value="Completed">Completed</option>
            </select>
            <select id="dv-client-filter"
                class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none">
                <option value="">All Clients</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}{{ $c->company ? ' — '.$c->company : '' }}</option>
                @endforeach
            </select>
            <button id="dv-refresh-btn"
                class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all">
                <i class="fas fa-redo-alt"></i>
            </button>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm">
        <div class="overflow-x-auto cursor-grab select-none global-drag-scroll">
            <table class="w-full text-left border-collapse" style="min-width: 1100px;">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" id="dv-select-all" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-4 w-28">Date</th>
                        <th class="px-4 py-4 w-36">Company</th>
                        <th class="px-4 py-4 w-36">Client</th>
                        <th class="px-4 py-4 w-28">Job #</th>
                        <th class="px-4 py-4 w-36">Barcode</th>
                        <th class="px-4 py-4 w-36">Pallet #</th>
                        <th class="px-4 py-4 w-56">Description</th>
                        <th class="px-4 py-4 w-20 text-center">Photos</th>
                        <th class="px-4 py-4 w-24 text-center">Items</th>
                        <th class="px-4 py-4 w-24 text-center">Net (lbs)</th>
                        <th class="px-4 py-4" style="width:11rem;min-width:11rem;">Status</th>
                        <th class="px-4 py-4 w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="dv-table-body">
                    <tr>
                        <td colspan="13" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4" id="dv-pagination"></div>
</div>

@push('scripts')
<script>
const dvRoutes = {
    loadData:    "{{ route('admin.downstream-vendor.load-data') }}",
    stats:       "{{ route('admin.downstream-vendor.stats') }}",
    exportCsv:   "{{ route('admin.downstream-vendor.export-csv') }}",
    updateStatus: id => `/admin/downstream-vendor/${id}/status`,
    delete:       id => `/admin/downstream-vendor/${id}`,
    bulkDelete:  "{{ route('admin.downstream-vendor.bulk-delete') }}",
};
const dvCsrf = "{{ csrf_token() }}";
</script>
<script>
(function () {
    let currentPage = 1;
    let selectedIds = new Set();
    let filters = { search: '', statusFilter: '', clientFilter: '' };

    const $  = id => document.getElementById(id);
    const ajax = (url, opts = {}) =>
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', ...opts.headers }, ...opts })
            .then(r => r.json());

    /* ── STATS ── */
    function loadStats() {
        fetch(dvRoutes.stats).then(r => r.json()).then(stats => {
            const c = $('dv-stats-container');
            c.innerHTML = stats.map(s => `
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 md:p-5 flex flex-col gap-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">${s.label}</span>
                    <span class="text-3xl font-black ${s.color}">${s.value}</span>
                </div>`).join('');
        });
    }

    /* ── STATUS BADGE ── */
    function statusBadge(status) {
        const map = {
            'Received':   'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Processing': 'bg-orange-50 text-orange-700 border-orange-200',
            'Completed':  'bg-purple-50 text-purple-700 border-purple-200',
        };
        return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black border ${map[status] || 'bg-slate-50 text-slate-600 border-slate-200'}">${status}</span>`;
    }

    /* ── LOAD DATA ── */
    function loadData(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({ page, ...filters });
        $('dv-table-body').innerHTML = `<tr><td colspan="13" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">Loading...</td></tr>`;

        fetch(`${dvRoutes.loadData}?${params}`).then(r => r.json()).then(data => {
            if (!data.items || data.items.length === 0) {
                $('dv-table-body').innerHTML = `<tr><td colspan="13" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">No records found</td></tr>`;
                $('dv-pagination').innerHTML = '';
                return;
            }

            $('dv-table-body').innerHTML = data.items.map(item => {
                const date = item.intake_date ? new Date(item.intake_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
                const photos = item.photos_count || 0;
                const isSelected = selectedIds.has(String(item.id));
                return `
                <tr class="hover:bg-slate-50/50 transition-colors ${isSelected ? 'bg-blue-50/40' : ''}">
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" class="dv-row-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                            data-id="${item.id}" ${isSelected ? 'checked' : ''}>
                    </td>
                    <td class="px-4 py-3 text-xs font-semibold text-slate-600">${date}</td>
                    <td class="px-4 py-3 text-xs font-bold text-slate-800">${item.company_name || '—'}</td>
                    <td class="px-4 py-3 text-xs text-slate-600">${item.client_name || '—'}</td>
                    <td class="px-4 py-3 text-xs font-mono text-slate-700">${item.job_no || '—'}</td>
                    <td class="px-4 py-3 text-xs font-mono text-slate-700">${item.barcode_number || '—'}</td>
                    <td class="px-4 py-3 text-xs font-mono text-slate-700">${item.pallet_number || '—'}</td>
                    <td class="px-4 py-3 text-xs text-slate-600 max-w-[14rem]">
                        <span class="line-clamp-2">${item.description || '—'}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        ${photos > 0
                            ? `<span class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 font-bold text-xs px-2 py-1 rounded-lg border border-blue-100"><i class="fas fa-image text-[10px]"></i>${photos}</span>`
                            : '<span class="text-slate-200 text-xs font-bold">—</span>'}
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-bold text-slate-700">${item.estimated_count ?? '—'}</td>
                    <td class="px-4 py-3 text-center text-sm font-bold text-slate-700">${item.net_weight != null ? parseFloat(item.net_weight).toFixed(2) : '—'}</td>
                    <td class="px-4 py-3">
                        <select class="dv-status-inline bg-transparent border-none text-xs font-black rounded-lg outline-none cursor-pointer" data-id="${item.id}">
                            ${['Received','Processing','Completed'].map(s =>
                                `<option value="${s}" ${item.status === s ? 'selected' : ''}>${s}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button class="dv-delete-btn bg-red-50 hover:bg-red-600 hover:text-white text-red-500 p-2 rounded-lg transition-all" data-id="${item.id}" title="Delete">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');

            renderPagination(data);
            attachRowEvents();
        });
    }

    function renderPagination(data) {
        if (data.last_page <= 1) { $('dv-pagination').innerHTML = ''; return; }
        let html = `<div class="flex items-center justify-between bg-white px-5 py-3 rounded-xl border border-slate-100 shadow-sm">
            <p class="text-xs text-slate-500 font-semibold">Showing ${((data.current_page-1)*data.per_page)+1}–${Math.min(data.current_page*data.per_page,data.total)} of <strong>${data.total}</strong></p>
            <div class="flex gap-1">`;
        for (let p = 1; p <= data.last_page; p++) {
            html += `<button class="dv-page-btn w-8 h-8 rounded-lg text-xs font-bold ${p === data.current_page ? 'bg-blue-600 text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'}" data-page="${p}">${p}</button>`;
        }
        html += '</div></div>';
        $('dv-pagination').innerHTML = html;
        document.querySelectorAll('.dv-page-btn').forEach(btn =>
            btn.addEventListener('click', () => loadData(parseInt(btn.dataset.page))));
    }

    function attachRowEvents() {
        // Inline status change
        document.querySelectorAll('.dv-status-inline').forEach(sel => {
            sel.addEventListener('change', e => {
                const id = sel.dataset.id;
                const fd = new FormData();
                fd.append('_token', dvCsrf);
                fd.append('status', sel.value);
                fetch(dvRoutes.updateStatus(id), { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(d => { if (d.success) loadData(currentPage); });
            });
        });

        // Row delete
        document.querySelectorAll('.dv-delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!confirm('Delete this shipment intake?')) return;
                fetch(dvRoutes.delete(btn.dataset.id), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': dvCsrf, 'X-Requested-With': 'XMLHttpRequest' }
                }).then(r => r.json()).then(d => { if (d.success) loadData(currentPage); });
            });
        });

        // Row checkbox
        document.querySelectorAll('.dv-row-select').forEach(cb => {
            cb.addEventListener('change', e => {
                const id = String(cb.dataset.id);
                e.target.checked ? selectedIds.add(id) : selectedIds.delete(id);
                updateBulkBar();
            });
        });
    }

    function updateBulkBar() {
        const n = selectedIds.size;
        const bar = $('dv-bulk-actions');
        if (n > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
        else        { bar.classList.add('hidden'); bar.classList.remove('flex'); }
        $('dv-bulk-count').textContent = `${n} selected`;
    }

    /* ── SELECT ALL ── */
    $('dv-select-all').addEventListener('change', e => {
        document.querySelectorAll('.dv-row-select').forEach(cb => {
            cb.checked = e.target.checked;
            e.target.checked ? selectedIds.add(String(cb.dataset.id)) : selectedIds.delete(String(cb.dataset.id));
        });
        updateBulkBar();
    });

    /* ── BULK STATUS ── */
    $('dv-bulk-status-btn').addEventListener('click', async () => {
        const status = $('dv-bulk-status-select').value;
        if (!status || selectedIds.size === 0) return;
        await Promise.all([...selectedIds].map(id => {
            const fd = new FormData();
            fd.append('_token', dvCsrf);
            fd.append('status', status);
            return fetch(dvRoutes.updateStatus(id), { method: 'POST', body: fd }).then(r => r.json());
        }));
        selectedIds.clear();
        updateBulkBar();
        loadData(currentPage);
    });

    /* ── BULK DELETE ── */
    $('dv-bulk-delete-btn').addEventListener('click', async () => {
        if (!confirm(`Delete ${selectedIds.size} record(s)?`)) return;
        const fd = new FormData();
        fd.append('_token', dvCsrf);
        [...selectedIds].forEach(id => fd.append('ids[]', id));
        const data = await fetch(dvRoutes.bulkDelete, { method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': dvCsrf, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ ids: [...selectedIds] })
        }).then(r => r.json());
        if (data.success) { selectedIds.clear(); updateBulkBar(); loadData(1); }
    });

    /* ── FILTERS ── */
    let searchTimer;
    $('dv-search').addEventListener('input', e => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { filters.search = e.target.value; loadData(1); }, 350);
    });
    $('dv-status-filter').addEventListener('change', e => { filters.statusFilter = e.target.value; loadData(1); });
    $('dv-client-filter').addEventListener('change', e => { filters.clientFilter = e.target.value; loadData(1); });
    $('dv-refresh-btn').addEventListener('click', () => { filters = { search: '', statusFilter: '', clientFilter: '' }; $('dv-search').value = ''; $('dv-status-filter').value = ''; $('dv-client-filter').value = ''; loadData(1); });

    /* ── EXPORT CSV ── */
    $('dv-export-csv-btn').addEventListener('click', () => { window.location.href = dvRoutes.exportCsv; });

    /* ── BOOT ── */
    loadStats();
    loadData(1);
})();
</script>
@endpush
@endsection
