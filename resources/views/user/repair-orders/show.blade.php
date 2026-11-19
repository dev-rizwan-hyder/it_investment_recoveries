@extends('user.layouts.app')

@section('title', 'Repair Order #' . $entry->id)

@section('content')
@php
    $statusBadgeClass = \App\Models\RepairEntry::statusBadgeClass($entry->status);
    $parsedMedia = $entry->parsed_photos;
    $clientName = $entry->customer_name ?: ($entry->client?->name ?: 'N/A');
    $serials = array_filter(array_map('trim', explode(',', $entry->serial_number ?? '')));
    $firstSerial = $serials[0] ?? ($entry->serial_number ?: null);
@endphp

<div class="min-h-screen bg-slate-50/50 py-6 sm:py-8 font-sans">
    <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Top Header & Actions --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div>
                <nav class="flex items-center text-[10px] font-black uppercase tracking-[0.25em] text-blue-600 mb-2">
                    <a href="{{ route('user.repair-orders.index') }}" class="hover:text-blue-800 transition-colors">Repair Orders</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-500 tracking-wider">Order Details</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <i class="fas fa-wrench text-amber-500"></i> Repair Order #{{ $entry->id }}
                    </h1>
                    {{-- HARD CODED STATUS BADGE: no option to change --}}
                    <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $statusBadgeClass }}">
                        {{ $entry->status_label }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Logged on {{ $entry->created_at ? $entry->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="printRepairSlipModal('{{ $entry->id }}')"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-xs transition-all">
                    <i class="fas fa-print mr-2"></i> Print Slip
                </button>

                @if($entry->sales_invoice)
                    <a href="{{ route('user.invoices.show', $entry->sales_invoice->id) }}"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs flex items-center shadow-xs transition-all">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> View Invoice
                    </a>
                @endif

                <a href="{{ route('user.repair-orders.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center transition-all">
                    <i class="fas fa-arrow-left mr-2 text-slate-500"></i> Back to Orders
                </a>
            </div>
        </div>

        {{-- SECTION 1: Pallet Barcode & Intake --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 space-y-4 shadow-xs">
            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-pallet text-blue-600"></i> Pallet & Intake Information
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Pallet Barcode --}}
                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pallet Barcode</div>
                        <div class="text-sm font-mono font-extrabold text-slate-800 truncate">
                            {{ $entry->pallet_info ?: 'No Pallet Assigned' }}
                        </div>
                    </div>
                </div>

                {{-- Job # --}}
                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Job #</div>
                        <div class="text-sm font-extrabold text-slate-800">
                            {{ $entry->job_no ? '#' . $entry->job_no : 'N/A' }}
                        </div>
                    </div>
                </div>

                {{-- Put-away Location --}}
                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Put-away Location</div>
                        <div class="text-sm font-bold text-amber-800">
                            {{ $entry->put_away_location ?: 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: Repair Item Details --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 space-y-4 shadow-xs">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-tools text-amber-500"></i> Repair Item Details
                </h4>
                <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-black border border-amber-200">
                    {{ count($entry->items_list) }} {{ count($entry->items_list) > 1 ? 'Items' : 'Item' }}
                </span>
            </div>

            <div class="space-y-4">
                @foreach ($entry->items_list as $idx => $item)
                    @php
                        $itemBarcode = !empty($item['barcode']) ? $item['barcode'] : ($idx === 0 ? ($entry->item_barcode ?: 'N/A') : 'N/A');
                        $itemSerial = !empty($item['serial']) ? $item['serial'] : ($idx === 0 ? ($entry->serial_number ?: 'N/A') : 'N/A');
                    @endphp
                    <div class="border border-slate-200 rounded-xl p-4 sm:p-5 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-800 text-white text-xs font-black flex items-center justify-center">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-extrabold text-sm text-slate-900">
                                    {{ $item['name'] ?: 'Repair Item' }}
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Item #{{ $idx + 1 }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <label class="text-[10px] font-bold uppercase text-slate-400 block">Item Name</label>
                                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $item['name'] ?: 'Repair Item' }}</div>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <label class="text-[10px] font-bold uppercase text-slate-400 block">Brand</label>
                                <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $item['brand'] ?: 'N/A' }}</div>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <label class="text-[10px] font-bold uppercase text-slate-400 block">Model</label>
                                <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $item['model'] ?: 'N/A' }}</div>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <label class="text-[10px] font-bold uppercase text-slate-400 block">Serial Number</label>
                                <div class="font-mono font-bold text-slate-800 text-sm mt-0.5">{{ $itemSerial }}</div>
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-barcode text-blue-600 text-base"></i>
                                <div>
                                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Item Barcode</span>
                                    <span class="font-mono font-bold text-blue-700 text-xs">{{ $itemBarcode }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION 4: Warehouse & Receiving --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 space-y-4 shadow-xs">
            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-warehouse text-blue-600"></i> Warehouse & Receiving
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Put-away Location</label>
                    <div class="font-bold text-amber-700 text-sm mt-1 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-amber-500"></i> {{ $entry->put_away_location ?: 'N/A' }}
                    </div>
                </div>

                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Marketing Reference</label>
                    <div class="font-bold text-slate-700 text-sm mt-1">
                        {{ $entry->marketing_ref ?: 'No reference' }}
                    </div>
                </div>

                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Receiving Employee</label>
                    <div class="font-bold text-slate-800 text-sm mt-1 flex items-center gap-1.5">
                        <i class="fas fa-user-check text-emerald-500"></i> {{ $entry->receiving_employee ?: 'N/A' }}
                    </div>
                </div>
            </div>

            {{-- Receiving Notes --}}
            <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/80">
                <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Receiving Notes</label>
                <div class="text-xs text-slate-700 leading-relaxed font-medium">
                    {{ $entry->receiving_notes ?: 'No receiving notes recorded.' }}
                </div>
            </div>

            {{-- Photos and Files --}}
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Photos & Files</label>
                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                        {{ count($parsedMedia) }} attached
                    </span>
                </div>

                @if (!empty($parsedMedia))
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach ($parsedMedia as $index => $media)
                            @if ($media['is_image'])
                                <div class="aspect-square rounded-xl border border-slate-200 overflow-hidden bg-white shadow-xs cursor-pointer group relative"
                                    onclick="window.openImageModal('{{ $media['url'] }}', 'Repair Order #{{ $entry->id }} - {{ $media['name'] }}')"
                                    title="Click to zoom image">
                                    <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                        onerror="this.src='/images/placeholder.png'">
                                    <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                        <i class="fas fa-search-plus"></i>
                                        <span>View</span>
                                    </div>
                                    <div class="absolute bottom-1 left-1 right-1 pointer-events-none">
                                        <span class="text-[9px] font-bold bg-black/60 text-white px-1.5 py-0.5 rounded truncate block text-center">
                                            {{ $media['name'] }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="aspect-square rounded-xl border border-slate-200 bg-slate-50 hover:bg-white shadow-xs p-3 flex flex-col justify-between items-center text-center cursor-pointer group transition-all hover:border-blue-400"
                                    onclick="window.viewDocument('{{ $media['raw'] }}', '{{ $media['name'] }}')"
                                    title="Click to view/download {{ $media['name'] }}">
                                    <div class="w-full flex justify-end">
                                        <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded border {{ $media['badge_class'] }}">
                                            {{ $media['label'] }}
                                        </span>
                                    </div>
                                    <div class="my-auto flex flex-col items-center px-1">
                                        <i class="{{ $media['icon'] }} text-2xl mb-1.5 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-xs font-bold text-slate-800 line-clamp-2 break-all group-hover:text-blue-600 leading-tight">
                                            {{ $media['name'] }}
                                        </span>
                                    </div>
                                    <div class="w-full text-center">
                                        <span class="text-[10px] font-bold text-blue-600 group-hover:underline inline-flex items-center gap-1">
                                            <i class="fas fa-external-link-alt text-[9px]"></i> Open
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl p-6 text-center text-slate-400 text-xs font-bold">
                        <i class="fas fa-folder-open text-xl mb-1.5 text-slate-300 block"></i>
                        No photos or files attached to this entry.
                    </div>
                @endif
            </div>
        </div>

        {{-- SECTION 5: Technician & Servicing Notes --}}
        <div class="bg-indigo-50/40 border border-indigo-100 rounded-2xl p-5 sm:p-6 space-y-4 shadow-xs">
            <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
                <h4 class="text-sm font-bold text-indigo-950 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-user-cog text-indigo-600"></i> Technician & Status Details
                </h4>
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">
                    Technician Servicing
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Tech Name --}}
                <div class="bg-white p-4 rounded-xl border border-indigo-100">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Assigned Technician</label>
                    <div class="font-bold text-indigo-950 text-sm mt-1 flex items-center gap-2">
                        <i class="fas fa-user-cog text-indigo-500"></i> {{ $entry->technician ?: 'N/A' }}
                    </div>
                </div>

                {{-- Repair Status (HARD CODED: read-only badge, no select) --}}
                <div class="bg-white p-4 rounded-xl border border-indigo-100">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Repair Status</label>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $statusBadgeClass }}">
                            <i class="fas fa-shield mr-1.5 text-[10px]"></i> {{ $entry->status_label }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium italic">(Current verified status)</span>
                    </div>
                </div>
            </div>

            {{-- Technician Notes --}}
            @if ($entry->technician_notes)
                <div class="bg-white p-4 rounded-xl border border-indigo-100">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Technician Notes</label>
                    <div class="text-xs text-slate-800 leading-relaxed font-medium">
                        {{ $entry->technician_notes }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Hidden Print Slip Container for this order (Prints all records in the view) --}}
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
                <div style="font-size: 10px; color: #94a3b8;">
                    Denver: {{ $entry->formatCreatedAtDenver('m/d/Y, h:i A T') }}
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

        {{-- Section 2: Repair Item Details (All Items in this order) --}}
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px;">
                <span style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #0f172a;">
                    2. Repair Item Details ({{ count($entry->items_list) }} {{ count($entry->items_list) > 1 ? 'Items' : 'Item' }})
                </span>
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

        {{-- Section 5: Attached Photos & Files Summary --}}
        @if (!empty($parsedMedia))
            <div style="margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px;">
                    5. Attached Photos & Files ({{ count($parsedMedia) }} attached)
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach ($parsedMedia as $media)
                        @if ($media['is_image'])
                            <div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px; text-align: center; width: 90px; background: #f8fafc;">
                                <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}" style="width: 100%; height: 60px; object-fit: cover; border-radius: 4px;">
                                <div style="font-size: 9px; font-weight: 700; color: #475569; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $media['name'] }}
                                </div>
                            </div>
                        @else
                            <div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; text-align: center; width: 110px; background: #f8fafc; font-size: 10px;">
                                <div style="font-weight: 800; color: #0284c7;">{{ $media['label'] }}</div>
                                <div style="font-size: 9px; color: #475569; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $media['name'] }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div style="border-top: 1px solid #cbd5e1; padding-top: 12px; margin-top: 24px; display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; font-weight: 600;">
            <span>Generated from IT Investment Recoveries Customer Portal</span>
            <span>Printed on {{ now()->format('M d, Y h:i A') }}</span>
        </div>
    </div>
</div>

<script>
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
