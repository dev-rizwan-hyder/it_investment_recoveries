@extends('user.layouts.app')

@section('title', 'Repair Orders')

@push('styles')
    <style>
        .portal-table-th {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            white-space: nowrap;
            padding: 14px 16px;
        }
        .portal-table-td {
            padding: 14px 16px;
            font-size: 13px;
        }
        @keyframes handSlideAnim {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(8px); }
        }
        .animate-hand-slide {
            display: inline-block;
            animation: handSlideAnim 1.4s ease-in-out infinite;
        }
    </style>
@endpush

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
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-gradient-to-tr from-amber-600/30 to-orange-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/3 h-80 w-80 rounded-full bg-gradient-to-tr from-amber-500/20 to-yellow-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between z-10">
            <div class="max-w-2xl space-y-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-400/30 text-xs font-bold uppercase tracking-widest text-amber-300">
                        <i class="fa-solid fa-screwdriver-wrench mr-1.5 text-amber-400"></i> Hardware Servicing & Repairs
                    </span>
                    @if ($client)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-xs font-bold text-emerald-300">
                            <i class="fa-solid fa-building mr-1.5 text-emerald-400"></i> Linked: {{ $client->name ?? $client->company }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl text-white">
                    Repair Orders
                </h1>
                <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                    Real-time tracking of hardware repair status, diagnostic progress, item details, put-away locations, and servicing notes.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-900/80 hover:bg-slate-800 text-xs font-bold text-slate-200 transition shadow-sm">
                    <i class="fa-solid fa-arrow-left text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Stats Cards Grid --}}
    @if ($stats)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Card 1: Received / In Progress -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Received / In Progress</p>
                        <p class="mt-1.5 text-2xl font-black text-blue-600">
                            {{ $stats['in_progress'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                        <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                    </span>
                </div>
            </div>

            <!-- Card 2: Ready for Pickup / Shipment -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Ready for Pickup / Shipment</p>
                        <p class="mt-1.5 text-2xl font-black text-amber-600">
                            {{ $stats['ready'] ?? 0 }}
                        </p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-100">
                        <i class="fa-solid fa-boxes-packing text-lg"></i>
                    </span>
                </div>
            </div>

            <!-- Card 3: Completed Repairs -->
            <div class="portal-card rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Completed Repairs</p>
                        <p class="mt-1.5 text-2xl font-black text-emerald-600">
                            {{ $stats['completed'] ?? 0 }}
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
        <div class="p-5 border-b border-slate-100 bg-slate-50/60">
            <form action="{{ route('user.repair-orders.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 justify-between items-center">
                <!-- Text Search Input -->
                <div class="relative w-full lg:w-96">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $searchTerm }}"
                           placeholder="Search device, serial, barcode, job #, tech..." 
                           class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Status Filter Dropdown -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label for="statusFilter" class="text-sm font-bold text-slate-600 whitespace-nowrap">Status:</label>
                        <select name="status" id="statusFilter" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition w-full sm:w-auto">
                            <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                            @foreach (\App\Models\RepairEntry::statusValues() as $statusKey)
                                @if (strtolower($statusKey) !== 'draft')
                                    <option value="{{ $statusKey }}" {{ $currentStatus === $statusKey ? 'selected' : '' }}>
                                        {{ \App\Models\RepairEntry::statusLabel($statusKey) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Pallet Filter Dropdown -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label for="palletFilter" class="text-sm font-bold text-slate-600 whitespace-nowrap">Pallet:</label>
                        <select name="pallet" id="palletFilter" onchange="this.form.submit()" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition w-full sm:w-auto">
                            <option value="all" {{ $currentPallet === 'all' ? 'selected' : '' }}>All Pallets</option>
                            @foreach ($clientPallets as $pal)
                                <option value="{{ $pal }}" {{ $currentPallet === $pal ? 'selected' : '' }}>
                                    {{ $pal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Filters Button -->
                    @if ($searchTerm !== '' || $currentStatus !== 'all' || $currentPallet !== 'all')
                        <a href="{{ route('user.repair-orders.index') }}" 
                           class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 rounded-xl text-sm font-bold transition flex items-center gap-1.5"
                           title="Clear all filters">
                            <i class="fa-solid fa-arrow-rotate-left text-xs"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Scrollable Table Wrapper with Drag-to-Scroll (Hand Cursor) --}}
        <div id="tableScrollWrapper" class="overflow-x-auto cursor-grab active:cursor-grabbing select-none transition-colors">
            <table class="w-full text-left border-collapse min-w-[1400px]">
                <thead>
                    <tr class="text-xs uppercase font-black text-slate-500 tracking-wider bg-slate-50/70 border-b border-slate-200">
                        <th class="px-5 py-4 whitespace-nowrap">Intake Date</th>
                        <th class="px-5 py-4 whitespace-nowrap">Job #</th>
                        <th class="px-5 py-4 whitespace-nowrap">Pallet Barcode</th>
                        <th class="px-5 py-4 whitespace-nowrap">Client / Vendor</th>
                        <th class="px-5 py-4 min-w-[200px]">Item Details</th>
                        <th class="px-5 py-4 min-w-[160px]">Serial Number</th>
                        <th class="px-5 py-4 whitespace-nowrap">Item Barcode</th>
                        <th class="px-5 py-4 whitespace-nowrap">Put-away Location</th>
                        <th class="px-5 py-4 whitespace-nowrap">Receiving Employee</th>
                        <th class="px-5 py-4 whitespace-nowrap">Tech Name</th>
                        <th class="px-5 py-4 text-center whitespace-nowrap">Photos &amp; Files</th>
                        <th class="px-5 py-4 text-center whitespace-nowrap" style="min-width: 11rem;">Repair Status</th>
                        <th class="px-5 py-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($entries as $entry)
                            @php
                                $statusBadgeClass = \App\Models\RepairEntry::statusBadgeClass($entry->status);
                                $parsedMedia = $entry->parsed_photos;
                                $mediaCount = count($parsedMedia);
                                $firstMedia = $parsedMedia[0] ?? null;

                                $serials = array_filter(array_map('trim', explode(',', $entry->serial_number ?? '')));
                                $firstSerial = $serials[0] ?? ($entry->serial_number ?: null);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- 1. Intake Date --}}
                                <td class="portal-table-td whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs">
                                        {{ $entry->created_at ? $entry->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium">
                                        {{ $entry->created_at ? $entry->created_at->format('h:i A') : '' }}
                                    </div>
                                </td>

                                {{-- 2. Job # --}}
                                <td class="portal-table-td whitespace-nowrap">
                                    @if ($entry->job_no)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            #{{ $entry->job_no }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">---</span>
                                    @endif
                                </td>

                                {{-- 3. Pallet Barcode --}}
                                <td class="portal-table-td whitespace-nowrap">
                                    @if ($entry->pallet_info)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            <i class="fas fa-pallet text-slate-400 text-[10px]"></i>
                                            {{ $entry->pallet_info }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">---</span>
                                    @endif
                                </td>

                                {{-- 4. Client / Vendor --}}
                                <td class="portal-table-td">
                                    <div class="font-bold text-slate-900 text-sm whitespace-nowrap">
                                        {{ $entry->customer_name ?: ($entry->client?->name ?: 'Client') }}
                                    </div>
                                    @if ($entry->client?->company)
                                        <div class="text-[11px] text-slate-500 font-medium truncate max-w-[180px]">
                                            {{ $entry->client->company }}
                                        </div>
                                    @endif
                                    <div class="text-[11px] text-slate-400 truncate max-w-[180px]">
                                        {{ $entry->customer_phone ?: ($entry->customer_email ?: '') }}
                                    </div>
                                </td>

                                {{-- 5. Item Details (Item Name, Brand, Model) --}}
                                <td class="portal-table-td">
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
                                <td class="portal-table-td whitespace-nowrap">
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
                                <td class="portal-table-td whitespace-nowrap">
                                    <div class="font-mono text-xs font-bold text-blue-700 bg-blue-50/70 border border-blue-100 px-2.5 py-1 rounded-lg inline-flex items-center gap-1.5">
                                        <i class="fas fa-barcode text-blue-500 text-[10px]"></i>
                                        {{ $entry->item_barcode ?: 'N/A' }}
                                    </div>
                                </td>

                                {{-- 8. Put-away Location --}}
                                <td class="portal-table-td whitespace-nowrap">
                                    <span class="text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-lg inline-flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-amber-500 text-[10px]"></i>
                                        {{ $entry->put_away_location ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- 9. Receiving Employee --}}
                                <td class="portal-table-td whitespace-nowrap">
                                    <span class="text-xs font-medium text-slate-700">
                                        {{ $entry->receiving_employee ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- 10. Tech Name --}}
                                <td class="portal-table-td whitespace-nowrap">
                                    <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg inline-flex items-center gap-1">
                                        <i class="fas fa-user-cog text-indigo-500 text-[10px]"></i>
                                        {{ $entry->technician ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- 11. Photos & Files --}}
                                <td class="portal-table-td text-center whitespace-nowrap">
                                    @if ($firstMedia)
                                        @if ($firstMedia['is_image'])
                                            <div class="inline-flex items-center gap-1.5 cursor-pointer group"
                                                 onclick="window.openImageModal('{{ $firstMedia['url'] }}', 'Repair Order #{{ $entry->id }} - {{ addslashes($entry->first_item_name) }}')"
                                                 title="Click to zoom image">
                                                <img src="{{ $firstMedia['url'] }}"
                                                     alt="Repair Media"
                                                     class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-200 group-hover:ring-2 group-hover:ring-blue-500 transition-all"
                                                     onerror="this.src='/images/placeholder.png'">
                                                @if ($mediaCount > 1)
                                                    <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full border border-slate-200">
                                                        +{{ $mediaCount - 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 cursor-pointer group"
                                                 onclick="window.viewDocument('{{ $firstMedia['raw'] }}', '{{ $firstMedia['name'] }}')"
                                                 title="Click to view {{ $firstMedia['name'] }}">
                                                <div class="h-9 px-2 rounded-lg border flex items-center gap-1.5 {{ $firstMedia['badge_class'] }} group-hover:border-blue-400 group-hover:shadow-sm transition-all">
                                                    <i class="{{ $firstMedia['icon'] }} text-xs"></i>
                                                    <span class="text-[11px] font-bold">{{ $firstMedia['label'] }}</span>
                                                </div>
                                                @if ($mediaCount > 1)
                                                    <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full border border-slate-200">
                                                        +{{ $mediaCount - 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400">No media</span>
                                    @endif
                                </td>

                                {{-- 12. Repair Status (HARD CODED: read-only badge, NO option to change) --}}
                                <td class="portal-table-td text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $statusBadgeClass }}">
                                        {{ $entry->status_label }}
                                    </span>
                                </td>

                                {{-- 13. Actions --}}
                                <td class="portal-table-td text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('user.repair-orders.show', $entry->id) }}"
                                           title="View Details"
                                           class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button"
                                                onclick="printRepairSlipModal('{{ $entry->id }}')"
                                                title="Print Label / Slip"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-100 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all text-xs">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>

                                    {{-- Hidden Print Slip Container for this entry (Full Record) --}}
                                    <div id="repair-slip-{{ $entry->id }}" class="hidden">
                                        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a; max-width: 800px; margin: 0 auto; padding: 24px;">
                                            {{-- Company & Order Header --}}
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f172a; padding-bottom: 16px; margin-bottom: 20px;">
                                                <div>
                                                    <div style="font-size: 20px; font-weight: 900; letter-spacing: -0.02em; color: #0f172a;">IT INVESTMENT RECOVERIES</div>
                                                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin-top: 2px;">Official Repair Order Slip / Report</div>
                                                    <div style="font-size: 12px; font-weight: 700; color: #0284c7; margin-top: 6px;">Repair Order #{{ $entry->id }}</div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <div style="display: inline-block; padding: 6px 14px; border-radius: 9999px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; background: #0f172a; color: #ffffff;">
                                                        STATUS: {{ $entry->status_label }}
                                                    </div>
                                                    <div style="font-size: 11px; color: #64748b; margin-top: 6px; font-weight: 600;">
                                                        Intake: {{ $entry->created_at ? $entry->created_at->format('M d, Y h:i A') : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Section 1: Pallet & Intake Information --}}
                                            <div style="margin-bottom: 20px;">
                                                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px;">
                                                    1. Pallet & Intake Information
                                                </div>
                                                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                                    <tr>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">Pallet Barcode</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; font-family: monospace; font-weight: 700; width: 25%;">{{ $entry->pallet_info ?: 'No Pallet Assigned' }}</td>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">Job #</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">{{ $entry->job_no ? '#' . $entry->job_no : 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700;">Put-away Location</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; font-weight: 700; color: #b45309;">{{ $entry->put_away_location ?: 'N/A' }}</td>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700;">Entry Date</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0;">{{ $entry->created_at ? $entry->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            {{-- Section 2: Repair Item Details --}}
                                            <div style="margin-bottom: 20px;">
                                                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px;">
                                                    2. Repair Item Details ({{ count($entry->items_list) }} {{ count($entry->items_list) > 1 ? 'Items' : 'Item' }})
                                                </div>
                                                <table style="width: 100%; border-collapse: collapse; font-size: 11px; text-align: left;">
                                                    <thead>
                                                        <tr style="background: #f1f5f9; color: #475569; font-weight: 800; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em;">
                                                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 35px; text-align: center;">#</th>
                                                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Item Name</th>
                                                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Brand</th>
                                                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Model</th>
                                                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Serial Number</th>
                                                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Item Barcode</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($entry->items_list as $idx => $item)
                                                            @php
                                                                $itemBarcode = !empty($item['barcode']) ? $item['barcode'] : ($idx === 0 ? ($entry->item_barcode ?: 'N/A') : 'N/A');
                                                                $itemSerial = !empty($item['serial']) ? $item['serial'] : ($idx === 0 ? ($entry->serial_number ?: 'N/A') : 'N/A');
                                                            @endphp
                                                            <tr style="{{ $idx % 2 === 1 ? 'background: #f8fafc;' : '' }}">
                                                                <td style="padding: 8px 10px; border: 1px solid #e2e8f0; text-align: center; font-weight: 800;">{{ $idx + 1 }}</td>
                                                                <td style="padding: 8px 10px; border: 1px solid #e2e8f0; font-weight: 800; color: #0f172a;">{{ $item['name'] ?: 'Repair Item' }}</td>
                                                                <td style="padding: 8px 10px; border: 1px solid #e2e8f0; font-weight: 600;">{{ $item['brand'] ?: 'N/A' }}</td>
                                                                <td style="padding: 8px 10px; border: 1px solid #e2e8f0; font-weight: 600;">{{ $item['model'] ?: 'N/A' }}</td>
                                                                <td style="padding: 8px 10px; border: 1px solid #e2e8f0; font-family: monospace; font-weight: 700;">{{ $itemSerial }}</td>
                                                                <td style="padding: 8px 10px; border: 1px solid #e2e8f0; font-family: monospace; font-weight: 700; color: #1d4ed8;">{{ $itemBarcode }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Section 3: Warehouse & Receiving Details --}}
                                            <div style="margin-bottom: 20px;">
                                                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px;">
                                                    3. Warehouse & Receiving Details
                                                </div>
                                                <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 8px;">
                                                    <tr>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">Put-away Location</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">{{ $entry->put_away_location ?: 'N/A' }}</td>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">Marketing Ref</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; width: 25%;">{{ $entry->marketing_ref ?: 'No reference' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700;">Receiving Employee</td>
                                                        <td colspan="3" style="padding: 8px 12px; border: 1px solid #e2e8f0; font-weight: 700;">{{ $entry->receiving_employee ?: 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; vertical-align: top;">Receiving Notes</td>
                                                        <td colspan="3" style="padding: 8px 12px; border: 1px solid #e2e8f0; line-height: 1.5; color: #334155;">
                                                            {{ $entry->receiving_notes ?: 'No receiving notes recorded.' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>

                                            {{-- Section 4: Technician & Servicing Details --}}
                                            <div style="margin-bottom: 20px;">
                                                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px;">
                                                    4. Technician & Status Details
                                                </div>
                                                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                                    <tr>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">Assigned Technician</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; font-weight: 800; color: #4338ca; width: 25%;">{{ $entry->technician ?: 'N/A' }}</td>
                                                        <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; width: 25%;">Repair Status</td>
                                                        <td style="padding: 8px 12px; border: 1px solid #e2e8f0; font-weight: 900; text-transform: uppercase; width: 25%;">{{ $entry->status_label }}</td>
                                                    </tr>
                                                    @if ($entry->technician_notes)
                                                        <tr>
                                                            <td style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; vertical-align: top;">Technician Notes</td>
                                                            <td colspan="3" style="padding: 8px 12px; border: 1px solid #e2e8f0; line-height: 1.5; color: #1e1b4b; background: #eef2ff;">
                                                                {{ $entry->technician_notes }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>

                                            {{-- Footer --}}
                                            <div style="border-top: 1px solid #cbd5e1; padding-top: 12px; margin-top: 24px; display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; font-weight: 600;">
                                                <span>Generated from IT Investment Recoveries Customer Portal</span>
                                                <span>Printed on {{ now()->format('M d, Y h:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 mb-3">
                                        <i class="fas fa-wrench text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-black text-slate-900">No Repair Orders Found</h3>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">There are no hardware repair orders recorded for your client profile matching this query.</p>
                                    @if ($searchTerm !== '' || $currentStatus !== 'all' || $currentPallet !== 'all')
                                        <a href="{{ route('user.repair-orders.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                                            <i class="fas fa-rotate-left text-[10px]"></i> Reset Filters
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if ($entries->hasPages())
                <div class="p-5 border-t border-slate-100 bg-slate-50/50">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

<script>
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

    function printRepairSlipModal(id) {
        const slip = document.getElementById('repair-slip-' + id);
        if (!slip) return;
        const printWindow = window.open('', '_blank', 'width=900,height=850');
        if (!printWindow) {
            window.print();
            return;
        }
        let html = '<!DOCTYPE html><html><head><title>Repair Order Report #' + id + '</title>';
        html += '<link rel="preconnect" href="https://fonts.googleapis.com">';
        html += '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        html += '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">';
        html += '<style>';
        html += 'body { font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #0f172a; margin: 0; padding: 20px; background: #fff; }';
        html += '*, *::before, *::after { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }';
        html += '@page { size: auto; margin: 12mm 15mm; }';
        html += '@media print { body { padding: 0 !important; } }';
        html += '</style></head><body>';
        html += slip.innerHTML;
        html += '<script>setTimeout(function() { window.print(); }, 400);<\/script></body></html>';
        printWindow.document.write(html);
        printWindow.document.close();
    }
</script>
@endsection
