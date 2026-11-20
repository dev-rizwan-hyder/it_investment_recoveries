@extends('layouts.admin')

@section('content')
    @php
        $repairStatuses = \App\Models\RepairEntry::statusChoices();
        $statusSelectClasses = \App\Models\RepairEntry::STATUS_SELECT_CLASSES;
    @endphp

    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-wrench text-amber-500"></i> Repair Intake Entries
            </h1>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-700">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Top Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 md:gap-4 mb-6" id="stats-container">
            @foreach ($stats as $stat)
                <a href="{{ route('admin.repair-intake.index', ['status' => $stat['status']]) }}"
                    class="bg-white p-4 md:p-5 rounded-xl border border-slate-100 shadow-sm transition-all hover:shadow-md relative overflow-hidden block group">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $stat['bg'] }}"></div>
                    <div class="pl-2">
                        <p class="text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 opacity-80 group-hover:text-slate-800">
                            {{ $stat['label'] }}
                        </p>
                        <h3 class="text-xl md:text-2xl font-black {{ $stat['color'] }} tracking-tight">
                            {{ $stat['value'] }}
                        </h3>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Action Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <a href="{{ route('admin.items-lot.intake', ['type' => 'Repair Intake']) }}"
                    class="bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center shadow-lg active:scale-95 transition-all">
                    <i class="fas fa-plus mr-2"></i> ADD REPAIR INTAKE
                </a>
                <button id="duplicateSelectedBtn" type="button" disabled
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-copy mr-2"></i> Duplicate (<span data-selected-repair-count>0</span>)
                </button>

                <button id="printSelectedLabelsBtn" type="button" disabled
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-tag mr-2"></i> Print Labels (<span data-selected-repair-count>0</span>)
                </button>

                <button id="printSelectedReceiptsBtn" type="button" disabled
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> Print Invoice Receipts (<span data-selected-repair-count>0</span>)
                </button>
                {{-- Export Button (Controller link) --}}
                <a href="{{ route('admin.repair.export', request()->all()) }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                    <i class="fas fa-file-export mr-2 text-slate-400"></i> Export CSV
                </a>

                <button
                    class="bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center ml-auto shadow-lg shadow-blue-100">
                    <i class="fas fa-sync-alt mr-2"></i> Sync to Shopify
                </button>
            </div>

            {{-- Filters --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-50">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Search by item, serial, barcode, job #, tech, client..."
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/10 outline-none">
                </div>

                <select id="statusFilter"
                    class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[170px]">
                    <option value="">All Statuses</option>
                    @foreach ($repairStatuses as $status => $label)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>

                <select id="palletFilter"
                    class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[160px]">
                    <option value="">All Pallets</option>
                    @foreach ($pallets_list ?? [] as $pallet)
                        <option value="{{ $pallet->barcode_number }}" {{ request('pallet') == $pallet->barcode_number ? 'selected' : '' }}>
                            {{ $pallet->barcode_number }}
                        </option>
                    @endforeach
                </select>

                <button onclick="window.location.href='{{ route('admin.repair-intake.index') }}'"
                    class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200">
                    <i class="fas fa-redo-alt"></i>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto cursor-grab select-none global-drag-scroll">
                <table class="w-full text-left border-collapse table-auto" style="min-width: 1450px;">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-[11px] uppercase font-bold text-slate-600 tracking-wider">
                            <th class="px-4 py-4 text-center w-12">
                                <input id="selectAllRepairs" type="checkbox"
                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    aria-label="Select all repair entries on this page"
                                    title="Select all repair entries on this page"
                                    {{ $entries->isEmpty() ? 'disabled' : '' }}>
                            </th>
                            <th class="px-4 py-4 whitespace-nowrap">Intake Date</th>
                            <th class="px-4 py-4 whitespace-nowrap">Job #</th>
                            <th class="px-4 py-4 whitespace-nowrap">Pallet Barcode</th>
                            <th class="px-4 py-4 whitespace-nowrap">Client / Vendor</th>
                            <th class="px-4 py-4 whitespace-nowrap">Item Details</th>
                            <th class="px-4 py-4 whitespace-nowrap">Serial Number</th>
                            <th class="px-4 py-4 whitespace-nowrap">Item Barcode</th>
                            <th class="px-4 py-4 whitespace-nowrap">Put-away Location</th>
                            <th class="px-4 py-4 whitespace-nowrap">Receiving Employee</th>
                            <th class="px-4 py-4 whitespace-nowrap">Tech Name</th>
                            <th class="px-4 py-4 text-center whitespace-nowrap">Photos & Files</th>
                            <th class="px-4 py-4 whitespace-nowrap" style="min-width: 11rem;">Repair Status</th>
                            <th class="px-4 py-4 text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-slate-50 transition-colors" data-repair-row="{{ $entry->id }}">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $entry->id }}"
                                        class="repair-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        aria-label="Select repair entry {{ $entry->id }}">
                                </td>

                                {{-- 1. Intake Date --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs">{{ $entry->created_at ? $entry->created_at->format('M d, Y') : 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium">{{ $entry->created_at ? $entry->created_at->format('h:i A') : '' }}</div>
                                </td>

                                {{-- 2. Job # --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($entry->job_no)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            #{{ $entry->job_no }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">---</span>
                                    @endif
                                </td>

                                {{-- 3. Pallet Barcode --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($entry->pallet_info)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            <i class="fas fa-pallet text-slate-400 text-[10px]"></i> {{ $entry->pallet_info }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">---</span>
                                    @endif
                                </td>

                                {{-- 4. Client / Vendor --}}
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-900 text-sm whitespace-nowrap">
                                        {{ $entry->customer_name ?: ($entry->client?->name ?: ($entry->user?->name ?? 'Walk-in Customer')) }}
                                    </div>
                                    @if ($entry->client?->company)
                                        <div class="text-[11px] text-slate-500 font-medium truncate max-w-[180px]">{{ $entry->client->company }}</div>
                                    @endif
                                    <div class="text-[11px] text-slate-400 truncate max-w-[180px]">
                                        {{ $entry->customer_phone ?: ($entry->customer_email ?: '') }}
                                    </div>
                                </td>

                                {{-- 5. Item Details (Item Name, Brand, Model) --}}
                                <td class="px-4 py-4">
                                    <div class="truncate max-w-[220px]" title="All items: {{ $entry->all_items_tooltip ?: $entry->device_type }}">
                                        <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5 leading-tight mb-0.5">
                                            <span class="truncate min-w-0">{{ $entry->first_item_name }}</span>
                                            @if ($entry->extra_items_count > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-black bg-blue-100 text-blue-700 border border-blue-200 shrink-0" title="All items: {{ $entry->all_items_tooltip }}">
                                                    + {{ $entry->extra_items_count }} more
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase truncate" title="{{ $entry->first_item_brand }}">
                                            {{ $entry->first_item_brand ?: 'Repair Item' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- 6. Serial Number --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php
                                        $serials = array_filter(array_map('trim', explode(',', $entry->serial_number ?? '')));
                                        $firstSerial = $serials[0] ?? $entry->serial_number;
                                    @endphp
                                    @if ($firstSerial)
                                        <div class="flex items-center gap-1" title="All serials: {{ implode(', ', $serials) }}">
                                            <span class="font-mono text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded">
                                                {{ $firstSerial }}
                                            </span>
                                            @if (count($serials) > 1)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                                                    + {{ count($serials) - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No Serial</span>
                                    @endif
                                </td>

                                {{-- 7. Item Barcode --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-mono text-xs font-bold text-blue-700 bg-blue-50/70 border border-blue-100 px-2.5 py-1 rounded-lg inline-flex items-center gap-1.5">
                                        <i class="fas fa-barcode text-blue-500 text-[10px]"></i> {{ $entry->item_barcode ?: 'N/A' }}
                                    </div>
                                </td>

                                {{-- 8. Put-away Location --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-lg inline-flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-amber-500 text-[10px]"></i> {{ $entry->put_away_location ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- 9. Receiving Employee --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-xs font-medium text-slate-700">{{ $entry->receiving_employee ?: 'N/A' }}</span>
                                </td>

                                {{-- 10. Tech Name --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg inline-flex items-center gap-1">
                                        <i class="fas fa-user-cog text-indigo-500 text-[10px]"></i> {{ $entry->technician ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- 11. Photos & Files --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                        $parsedMedia = $entry->parsed_photos;
                                        $mediaCount = count($parsedMedia);
                                        $firstMedia = $parsedMedia[0] ?? null;
                                        $imagesOnly = $entry->image_photos;
                                        $galleryJson = json_encode(array_values(array_map(fn($m) => $m['url'], $imagesOnly)));
                                    @endphp
                                    @if ($firstMedia)
                                        @if ($firstMedia['is_image'])
                                            <div class="inline-flex items-center gap-1.5 cursor-pointer group" onclick="openImageGallery({{ $galleryJson }}, 'Repair Entry #{{ $entry->id }}')" title="Click to view images">
                                                <img src="{{ $firstMedia['url'] }}" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-200 group-hover:ring-2 group-hover:ring-blue-500 transition-all" onerror="this.src='/images/placeholder.png'">
                                                @if ($mediaCount > 1)
                                                    <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full border border-slate-200">+{{ $mediaCount - 1 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 cursor-pointer group" onclick="openDocumentFile('{{ $firstMedia['raw'] }}', '{{ $firstMedia['name'] }}')" title="Click to view {{ $firstMedia['name'] }}">
                                                <div class="h-9 px-2 rounded-lg border flex items-center gap-1.5 {{ $firstMedia['badge_class'] }} group-hover:border-blue-400 group-hover:shadow-sm transition-all">
                                                    <i class="{{ $firstMedia['icon'] }} text-xs"></i>
                                                    <span class="text-[11px] font-bold">{{ $firstMedia['label'] }}</span>
                                                </div>
                                                @if ($mediaCount > 1)
                                                    <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full border border-slate-200">+{{ $mediaCount - 1 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400">No media</span>
                                    @endif
                                </td>

                                {{-- 12. Repair Status --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                        $statusClass = $statusSelectClasses[$entry->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                    @endphp
                                    <select data-status-select
                                        data-original-status="{{ $entry->status }}"
                                        data-update-url="{{ route('admin.repair.entries.update-status', $entry->id) }}"
                                        class="min-w-[150px] rounded-lg border px-3 py-2 text-xs font-bold uppercase outline-none transition-all {{ $statusClass }}">
                                        @if ($entry->status && !array_key_exists($entry->status, $repairStatuses))
                                            <option value="{{ $entry->status }}" selected>{{ ucwords(str_replace('-', ' ', $entry->status)) }}</option>
                                        @endif
                                        @foreach ($repairStatuses as $status => $label)
                                            <option value="{{ $status }}" {{ $entry->status === $status ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div data-status-feedback class="mt-1 h-4 text-[10px] font-bold text-emerald-600"></div>
                                </td>

                                {{-- 13. Actions --}}
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('admin.repair.entries.show', $entry->id) }}" title="View"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.repair.entries.edit', $entry->id) }}" title="Edit"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-100 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all text-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" onclick="printRepairSlip('repair-slip-{{ $entry->id }}')" title="Print Label"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-orange-100 bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all text-xs">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <form action="{{ route('admin.repair.entries.destroy', $entry->id) }}" method="POST" class="inline-flex"
                                            onsubmit="return confirm('Are you sure you want to delete this repair entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        {{-- Hidden Print Template --}}
                                        <div id="repair-slip-{{ $entry->id }}" class="hidden">
                                            @php
                                                $printBarcode = $entry->item_barcode ?: ($entry->serial_number ?: 'REP-' . $entry->id);
                                                $printCategory = $entry->brand_category ?: ($entry->device_type ?: 'Repair Item');
                                                $printNotes = \Illuminate\Support\Str::limit($entry->notes ?: 'N/A', 56, '');
                                                $printCustomerName = $entry->customer_name ?: ($entry->client?->name ?: ($entry->user?->name ?? 'N/A'));
                                                $printCustomerPhone = $entry->customer_phone ?: 'N/A';
                                                $printCustomerEmail = \Illuminate\Support\Str::limit($entry->customer_email ?: 'N/A', 42, '');
                                            @endphp
                                            <div class="label-container">
                                                <div class="label-header">
                                                    <h1>REPAIR LABEL</h1>
                                                </div>
                                                <div class="barcode-section">
                                                    <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ urlencode($printBarcode) }}&scale=2&height=10&includetext=false" alt="Barcode">
                                                    <div class="barcode-number">{{ $printBarcode }}</div>
                                                </div>
                                                <div class="detail-grid">
                                                    <div class="detail-item wide">
                                                        <span class="detail-label">Customer</span>
                                                        <span class="detail-value">{{ $printCustomerName }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Phone</span>
                                                        <span class="detail-value">{{ $printCustomerPhone }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Email</span>
                                                        <span class="detail-value">{{ $printCustomerEmail }}</span>
                                                    </div>
                                                    <div class="detail-item wide">
                                                        <span class="detail-label">Item / Device</span>
                                                        <span class="detail-value">{{ $entry->device_type ?: 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Brand</span>
                                                        <span class="detail-value">{{ $entry->brand_category ?: 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Serial #</span>
                                                        <span class="detail-value">{{ $entry->serial_number ?: 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Pallet #</span>
                                                        <span class="detail-value">{{ $entry->pallet_info ?: 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Location</span>
                                                        <span class="detail-value">{{ $entry->put_away_location ?: 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item wide">
                                                        <span class="detail-label">Notes</span>
                                                        <span class="detail-value">{{ $printNotes }}</span>
                                                    </div>
                                                    <div class="detail-item wide">
                                                        <span class="detail-label">Technician</span>
                                                        <span class="detail-value">{{ $entry->technician ?: 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    Denver: {{ now('America/Denver')->format('m/d/Y, g:i A T') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="p-12 text-center text-slate-400 font-bold uppercase text-xs">
                                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                        <i class="fas fa-tools text-xl"></i>
                                    </div>
                                    No Repair Intake Entries Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
    @include('admin.repairs.add_modal')
    @include('admin.partials.image-gallery-modal')
    <script>
        function openDocumentFile(dataUriOrUrl, fileName = 'document') {
            if (!dataUriOrUrl) return;

            if (!dataUriOrUrl.startsWith('data:')) {
                window.open(dataUriOrUrl, '_blank');
                return;
            }

            try {
                const parts = dataUriOrUrl.split(';base64,');
                const contentType = parts[0].split(':')[1] || 'application/octet-stream';
                const byteCharacters = atob(parts[1]);
                const byteArrays = [];

                const sliceSize = 512;
                for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                    const slice = byteCharacters.slice(offset, offset + sliceSize);
                    const byteNumbers = new Array(slice.length);
                    for (let i = 0; i < slice.length; i++) {
                        byteNumbers[i] = slice.charCodeAt(i);
                    }
                    const byteArray = new Uint8Array(byteNumbers);
                    byteArrays.push(byteArray);
                }

                const blob = new Blob(byteArrays, { type: contentType });
                const blobUrl = URL.createObjectURL(blob);

                if (contentType.includes('pdf')) {
                    window.open(blobUrl, '_blank');
                } else {
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            } catch (e) {
                console.error('Error opening file:', e);
                const win = window.open();
                if (win) {
                    win.document.write('<iframe src="' + dataUriOrUrl + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
                }
            }
        }

        function repairLabelPrintStyles() {
            return `
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                @page {
                    size: 3in 3in;
                    margin: 0;
                }
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    background: white;
                    color: #111827;
                }
                .print-preview-body {
                    min-height: 100vh;
                    background: #f8fafc;
                    padding: 30px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                .btn-print {
                    background: #2563eb;
                    color: white;
                    border: none;
                    padding: 12px 34px;
                    border-radius: 8px;
                    font-weight: 800;
                    cursor: pointer;
                    margin-bottom: 25px;
                    text-transform: uppercase;
                }
                .label-page {
                    page-break-after: always;
                    display: flex;
                    justify-content: center;
                    margin-bottom: 24px;
                }
                .label-page:last-child {
                    page-break-after: auto;
                }
                .label-container {
                    width: 3in;
                    height: 3in;
                    margin: 0 auto;
                    background: white;
                    padding: 0.08in;
                    border: 1px solid #111;
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                }
                .label-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 1px solid #0071e3;
                    padding-bottom: 0.035in;
                    margin-bottom: 0.04in;
                }
                .label-header h1 {
                    font-size: 9px;
                    color: #111827;
                    font-weight: 900;
                    letter-spacing: 0;
                    line-height: 1;
                }
                .barcode-section {
                    text-align: center;
                    margin: 0;
                    padding: 0.035in 0.04in 0.025in;
                    background: #f8fafc;
                    border: 1px solid #d1d5db;
                }
                .barcode-section img {
                    max-width: 100%;
                    height: auto;
                    max-height: 0.32in;
                    margin-bottom: 0.02in;
                }
                .barcode-number {
                    font-family: "Courier New", monospace;
                    font-size: 7px;
                    font-weight: bold;
                    letter-spacing: 0;
                    color: #111827;
                    line-height: 1;
                }
                .detail-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0.025in 0.05in;
                    margin-top: 0.04in;
                    flex: 1;
                    min-height: 0;
                }
                .detail-item {
                    min-width: 0;
                    border-bottom: 1px solid #e5e7eb;
                    padding-bottom: 0.012in;
                }
                .detail-item.wide {
                    grid-column: 1 / -1;
                }
                .detail-label {
                    display: block;
                    font-weight: bold;
                    color: #0071e3;
                    text-transform: uppercase;
                    letter-spacing: 0;
                    font-size: 5.5px;
                    line-height: 1.05;
                }
                .detail-value {
                    display: block;
                    font-weight: 600;
                    color: #111827;
                    font-size: 7px;
                    line-height: 1.12;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .footer {
                    text-align: center;
                    margin-top: 0.025in;
                    padding-top: 0.018in;
                    border-top: 1px dashed #ccc;
                    font-size: 5.5px;
                    color: #6b7280;
                    line-height: 1;
                }
                @media print {
                    html,
                    body {
                        width: 3in;
                        height: 3in;
                        background: white;
                    }
                    .print-preview-body {
                        min-height: 0;
                        background: white;
                        padding: 0;
                        display: block;
                    }
                    .btn-print {
                        display: none;
                    }
                    .label-page {
                        margin: 0;
                    }
                }
            `;
        }

        function printRepairSlip(containerId) {
            const data = document.getElementById(containerId).innerHTML;
            const printWindow = window.open('', '_blank', 'height=800,width=900');

            if (!printWindow) {
                alert('Allow pop-ups to print the repair label.');
                return;
            }

            printWindow.document.write(`
        <html>
            <head>
                <title>Print Repair Label</title>
                <style>
                    ${repairLabelPrintStyles()}
                </style>
            </head>
            <body class="print-preview-body">
                <button class="btn-print" onclick="window.print()">Confirm & Print Label</button>
                <div class="label-page">${data}</div>
            </body>
        </html>
    `);
            printWindow.document.close();
        }

        function openModal() {
            const modal = document.getElementById('addEntryModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal() {
            document.getElementById('addEntryModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function triggerFileInput() {
            document.getElementById('photoInput').click();
        }

        function updatePhotoStatus() {
            const input = document.getElementById('photoInput');
            const status = document.getElementById('photoStatus');
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = "";

            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

            if (input.files.length > 0) {
                let validFiles = true;

                Array.from(input.files).forEach(file => {
                    if (!allowedTypes.includes(file.type)) {
                        validFiles = false;
                    }
                });

                if (!validFiles) {
                    alert("Only Images Type (JPG, PNG, WEBP) are allowed!");
                    input.value = ""; // Input clear kar dein
                    status.innerText = "No photos uploaded";
                    return;
                }

                status.innerText = input.files.length + " photo(s) selected";
                status.className = "text-[11px] text-blue-600 font-bold mt-2";
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = "relative";
                        div.innerHTML =
                            `<img src="${e.target.result}" class="w-16 h-16 rounded-lg object-cover border border-slate-200 shadow-sm">`;
                        preview.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const palletFilter = document.getElementById('palletFilter');
            const selectAllRepairs = document.getElementById('selectAllRepairs');
            const repairCheckboxes = Array.from(document.querySelectorAll('.repair-checkbox'));
            const selectedCountEls = Array.from(document.querySelectorAll('[data-selected-repair-count]'));
            const duplicateSelectedBtn = document.getElementById('duplicateSelectedBtn');
            const printSelectedLabelsBtn = document.getElementById('printSelectedLabelsBtn');
            const printSelectedReceiptsBtn = document.getElementById('printSelectedReceiptsBtn');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const statusClassMap = @json($statusSelectClasses);
            const statusBaseClass = 'min-w-[150px] rounded-lg border px-3 py-2 text-xs font-bold uppercase outline-none transition-all';
            const defaultStatusClass = 'bg-slate-50 text-slate-700 border-slate-200';

            function applyFilters() {
                const search = searchInput.value;
                const status = statusFilter.value;
                const pallet = palletFilter ? palletFilter.value : '';
                let url = new URL(window.location.href);

                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');

                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');

                if (pallet) url.searchParams.set('pallet', pallet);
                else url.searchParams.delete('pallet');

                url.searchParams.delete('page'); // Filter change hone par pehle page par jayein
                window.location.href = url.href;
            }

            // Enter key press par search karein
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applyFilters();
            });

            // Dropdown change par foran filter karein
            statusFilter.addEventListener('change', applyFilters);
            if (palletFilter) palletFilter.addEventListener('change', applyFilters);

            function getSelectedRepairIds() {
                return repairCheckboxes
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);
            }

            function updateSelectedRepairState() {
                const selectedIds = getSelectedRepairIds();
                const selectedCount = selectedIds.length;
                const allSelected = repairCheckboxes.length > 0 && selectedCount === repairCheckboxes.length;

                selectedCountEls.forEach((element) => {
                    element.textContent = selectedCount;
                });

                [duplicateSelectedBtn, printSelectedLabelsBtn, printSelectedReceiptsBtn].forEach((button) => {
                    if (button) button.disabled = selectedCount === 0;
                });

                if (selectAllRepairs) {
                    selectAllRepairs.checked = allSelected;
                    selectAllRepairs.indeterminate = selectedCount > 0 && !allSelected;
                }
            }

            if (selectAllRepairs) {
                selectAllRepairs.addEventListener('change', function() {
                    repairCheckboxes.forEach((checkbox) => {
                        checkbox.checked = selectAllRepairs.checked;
                    });
                    updateSelectedRepairState();
                });
            }

            repairCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateSelectedRepairState);
            });

            function printSelectedRepairSlips() {
                const selectedIds = getSelectedRepairIds();
                if (selectedIds.length === 0) return;

                const slips = selectedIds
                    .map((id) => document.getElementById(`repair-slip-${id}`)?.innerHTML || '')
                    .filter(Boolean);

                if (slips.length === 0) return;

                const printWindow = window.open('', '_blank', 'height=900,width=900');
                if (!printWindow) {
                    alert('Allow pop-ups to print selected repair labels.');
                    return;
                }

                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print Repair Labels</title>
                            <style>
                                ${repairLabelPrintStyles()}
                            </style>
                        </head>
                        <body class="print-preview-body">
                            <button class="btn-print" onclick="window.print()">Confirm & Print Selected</button>
                            ${slips.map((slip) => `<div class="label-page">${slip}</div>`).join('')}
                        </body>
                    </html>
                `);
                printWindow.document.close();
            }

            if (printSelectedLabelsBtn) {
                printSelectedLabelsBtn.addEventListener('click', printSelectedRepairSlips);
            }

            if (duplicateSelectedBtn) {
                duplicateSelectedBtn.addEventListener('click', function() {
                    alert('Duplicate selected repair entries is not connected yet.');
                });
            }

            if (printSelectedReceiptsBtn) {
                printSelectedReceiptsBtn.addEventListener('click', printSelectedRepairSlips);
            }

            function showFeedback(container, message, isError = false) {
                if (!container) return;

                container.textContent = message;
                container.className = `mt-1 h-4 text-[10px] font-bold ${isError ? 'text-red-600' : 'text-emerald-600'}`;

                setTimeout(() => {
                    container.textContent = '';
                }, 1800);
            }

            async function patchRepair(url, payload) {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || data.success === false) {
                    throw new Error(data.message || 'Update failed.');
                }

                return data;
            }

            function applyStatusClass(select) {
                select.className = `${statusBaseClass} ${statusClassMap[select.value] || defaultStatusClass}`;
            }

            document.querySelectorAll('[data-status-select]').forEach((select) => {
                applyStatusClass(select);

                select.addEventListener('change', async function() {
                    const previousStatus = select.dataset.originalStatus || '';
                    const feedback = select.closest('td')?.querySelector('[data-status-feedback]');

                    select.disabled = true;
                    select.classList.add('opacity-70');

                    try {
                        const data = await patchRepair(select.dataset.updateUrl, {
                            status: select.value,
                        });

                        select.dataset.originalStatus = select.value;
                        applyStatusClass(select);
                        const feedbackMsg = data.email_sent === true 
                            ? 'Updated & Email Sent' 
                            : (data.email_message && data.email_message.includes('no customer email') ? 'Updated (no email)' : (data.email_sent === false ? 'Email failed' : 'Updated'));
                        showFeedback(feedback, feedbackMsg, data.email_sent === false && !(data.email_message && data.email_message.includes('no customer email')));
                    } catch (error) {
                        select.value = previousStatus;
                        applyStatusClass(select);
                        showFeedback(feedback, error.message || 'Failed', true);
                    } finally {
                        select.disabled = false;
                        select.classList.remove('opacity-70');
                    }
                });
            });

            document.querySelectorAll('[data-quantity-input]').forEach((input) => {
                input.dataset.originalQuantity = input.value || '1';

                async function saveQuantity() {
                    const feedback = input.closest('td')?.querySelector('[data-quantity-feedback]');
                    const quantity = Number(input.value);

                    if (!Number.isInteger(quantity) || quantity < 1) {
                        input.value = input.dataset.originalQuantity || '1';
                        showFeedback(feedback, 'Min 1', true);
                        return;
                    }

                    if (String(quantity) === String(input.dataset.originalQuantity)) {
                        return;
                    }

                    input.disabled = true;
                    input.classList.add('opacity-70');

                    try {
                        const data = await patchRepair(input.dataset.updateUrl, {
                            quantity,
                        });

                        input.value = data.quantity || quantity;
                        input.dataset.originalQuantity = String(input.value);
                        showFeedback(feedback, 'Saved');
                    } catch (error) {
                        input.value = input.dataset.originalQuantity || '1';
                        showFeedback(feedback, error.message || 'Failed', true);
                    } finally {
                        input.disabled = false;
                        input.classList.remove('opacity-70');
                    }
                }

                input.addEventListener('change', saveQuantity);
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        input.blur();
                    }
                });
            });

            updateSelectedRepairState();
        });
    </script>
@endsection
