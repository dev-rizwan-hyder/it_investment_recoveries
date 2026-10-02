@extends('user.layouts.app')

@section('title', 'IT Asset Details - ' . $item->barcode)

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-6">
    {{-- Breadcrumb & Back --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.it-assets.index') }}"
               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Certificate of IT Assets Management</span>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Certificate # {{ $item->barcode }}</h1>
            </div>
        </div>
        <div class="no-print">
            <button onclick="openPrintModal()" type="button" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-extrabold transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Print / Save PDF</span>
            </button>
        </div>
    </div>

    {{-- Top Section Grid: Specs (2 cols) & Photos (1 col at top right) --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 items-start">
        {{-- Main Specs Card --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="portal-card rounded-3xl p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-6">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h2 class="text-xl font-black text-slate-900">{{ $item->primary_name }}</h2>
                            @if ($item->total_parsed_count > 1)
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Contains {{ $item->total_parsed_count }} Item Models
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1.5">
                            Brand: <span class="text-slate-800 font-extrabold">{{ $item->primary_brand }}</span>
                            <span class="mx-2 text-slate-200">|</span>
                            Model: <span class="text-slate-800 font-extrabold">{{ $item->primary_model }}</span>
                        </p>
                    </div>
                    <div>
                        @php
                            $rawStatus = strtolower(trim($item->status ?? ''));
                            if (str_contains($rawStatus, 'complet') || str_contains($rawStatus, 'ready')) {
                                $statusDisplay = 'COMPLETED';
                                $badgeStyle = 'bg-[#10b981] text-white';
                            } elseif (str_contains($rawStatus, 'progr') || str_contains($rawStatus, 'process')) {
                                $statusDisplay = 'PROCESSING';
                                $badgeStyle = 'bg-[#ff6b00] text-white';
                            } else {
                                $statusDisplay = 'RECEIVED';
                                $badgeStyle = 'bg-[#357af6] text-white';
                            }
                        @endphp
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white shadow-xs {{ $badgeStyle }}">
                            {{ $statusDisplay }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Category</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->category ?: 'IT Assets' }}</p>
                    </div>
                    @if ($item->sub_category)
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Sub-category</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->sub_category }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Condition</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->condition ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Quantity</p>
                        <p class="mt-1 text-lg font-black text-slate-900">{{ $item->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Weight</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->weight ?: 0 }} lbs</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Pallet Ref</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-box text-slate-400"></i>
                            {{ $item->pallet_number ?: 'Unassigned' }}
                        </p>
                    </div>
                    @if ($item->item_placement)
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Placement Location</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->item_placement }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Registered Date</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                @if ($item->notes)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <p class="text-xs font-extrabold text-slate-400 uppercase">Notes & Special Instructions</p>
                        <p class="mt-2 text-slate-700 text-xs sm:text-sm leading-relaxed font-medium bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            {{ $item->notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Photos Column (Top Right Side) --}}
        <div class="lg:col-span-1">
            <div class="portal-card rounded-3xl p-6">
                <h3 class="text-sm font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-images text-slate-400"></i>
                    Photos & Files
                </h3>

                @php
                    $photos = [];
                    if ($item->photos_paths) {
                        $photos = is_string($item->photos_paths) ? json_decode($item->photos_paths, true) : $item->photos_paths;
                    }
                @endphp

                @if (!empty($photos) && is_array($photos))
                    <div class="grid grid-cols-2 gap-3 items-start">
                        @foreach ($photos as $photo)
                            @php
                                $isImage = str_starts_with($photo, 'data:image/') || preg_match('/\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i', strtok($photo, '?'));
                                $photoSrc = (str_starts_with($photo, 'data:') || str_starts_with($photo, 'http')) 
                                            ? $photo 
                                            : (str_starts_with($photo, '/') ? $photo : asset($photo));
                                
                                $filename = 'File';
                                if (str_starts_with($photo, 'data:')) {
                                    $mime = substr($photo, 5, strpos($photo, ';') - 5);
                                    if (str_contains($mime, 'pdf')) $filename = 'document.pdf';
                                    elseif (str_contains($mime, 'csv')) $filename = 'document.csv';
                                    elseif (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet') || str_contains($mime, 'sheet')) $filename = 'document.xlsx';
                                    elseif (str_contains($mime, 'word') || str_contains($mime, 'processing')) $filename = 'document.docx';
                                    else $filename = 'attachment';
                                } else {
                                    $filename = basename($photo);
                                }
                            @endphp

                            @if ($isImage)
                                <div class="group relative flex flex-col justify-between h-44 w-full rounded-2xl border border-slate-200/80 bg-slate-50 p-2.5 transition hover:border-indigo-300 hover:shadow-xs">
                                    <div class="relative h-28 w-full overflow-hidden rounded-xl bg-slate-100/90 flex items-center justify-center">
                                        <img src="{{ $photoSrc }}" class="h-full w-full object-contain p-1 transition duration-300 group-hover:scale-105" alt="{{ $filename }}">
                                    </div>
                                    <div class="flex items-center justify-between gap-1.5 pt-2">
                                        <button type="button" onclick="window.openImageModal('{{ $photoSrc }}', '{{ addslashes($filename) }}')" 
                                                class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-eye text-[10px]"></i> View
                                        </button>
                                        <a href="{{ $photoSrc }}" download="{{ $filename }}" target="_blank" 
                                           class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-download text-[10px]"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @else
                                @php
                                    $iconClass = 'fa-file-lines text-indigo-500';
                                    if (str_contains(strtolower($photo), 'pdf') || str_contains(strtolower($filename), 'pdf')) {
                                        $iconClass = 'fa-file-pdf text-rose-500';
                                    } elseif (str_contains(strtolower($photo), 'csv') || str_contains(strtolower($photo), 'excel') || str_contains(strtolower($filename), 'xlsx') || str_contains(strtolower($filename), 'xls')) {
                                        $iconClass = 'fa-file-excel text-emerald-500';
                                    } elseif (str_contains(strtolower($photo), 'word') || str_contains(strtolower($filename), 'doc') || str_contains(strtolower($filename), 'docx')) {
                                        $iconClass = 'fa-file-word text-blue-600';
                                    }
                                @endphp
                                <div class="group relative flex flex-col justify-between h-44 w-full rounded-2xl border border-slate-200/80 bg-slate-50/80 p-2.5 transition hover:border-indigo-300 hover:bg-slate-50 hover:shadow-xs">
                                    <div class="flex flex-col items-center justify-center h-28 w-full rounded-xl bg-white border border-slate-100 p-2 text-center">
                                        <i class="fa-solid {{ $iconClass }} text-3xl mb-1.5 transition duration-300 group-hover:scale-110"></i>
                                        <span class="text-[11px] font-extrabold text-slate-800 truncate w-full px-1" title="{{ $filename }}">{{ $filename }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1.5 pt-2">
                                        <button type="button" onclick="window.viewDocument('{{ $photoSrc }}', '{{ addslashes($filename) }}')" 
                                                class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-eye text-[10px]"></i> View
                                        </button>
                                        <a href="{{ $photoSrc }}" download="{{ $filename }}" 
                                           class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-download text-[10px]"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-regular fa-image text-slate-300 text-2xl mb-2"></i>
                        <p class="text-slate-400 text-xs font-semibold">No photos attached for this asset</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Section: Serialized Item Models Breakdown (Full Width) --}}
    <div class="portal-card rounded-3xl p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-black text-slate-900">Serialized Item Models Breakdown</h3>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ count($item->parsed_items) }} {{ Str::plural('Item Model', count($item->parsed_items)) }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Individual unit specifications and logged serial numbers</p>
            </div>

            @if(count($item->parsed_items) > 3)
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           id="breakdownSearchInput" 
                           placeholder="Filter item models..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition">
                </div>
            @endif
        </div>

        <div id="breakdownList" class="space-y-4 max-h-[800px] overflow-y-auto pr-1">
            @foreach ($item->parsed_items as $subItem)
                <div class="breakdown-card relative p-5 rounded-2xl border border-slate-200/80 bg-slate-50/40 hover:bg-slate-50/80 transition duration-200 space-y-3"
                     data-search="{{ strtolower($subItem['name'].' '.$subItem['brand'].' '.$subItem['model'].' '.$subItem['serial_number']) }}">
                    
                    {{-- Index Number Badge --}}
                    <div class="absolute right-4 top-4">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full text-xs font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                            {{ $subItem['index'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Item Name --}}
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Item Name</label>
                            <div class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 shadow-2xs truncate" title="{{ $subItem['name'] }}">
                                {{ $subItem['name'] }}
                            </div>
                        </div>

                        {{-- Brand --}}
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Brand</label>
                            <div class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 shadow-2xs truncate" title="{{ $subItem['brand'] }}">
                                {{ $subItem['brand'] }}
                            </div>
                        </div>

                        {{-- Model --}}
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Model</label>
                            <div class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 shadow-2xs truncate" title="{{ $subItem['model'] }}">
                                {{ $subItem['model'] }}
                            </div>
                        </div>

                        {{-- Serial Number --}}
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Serial Number</label>
                            @if ($subItem['serial_number'])
                                <div class="flex items-center justify-between w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 shadow-2xs group cursor-pointer hover:border-indigo-300 transition"
                                     onclick="window.copyToClipboard('{{ $subItem['serial_number'] }}', 'Serial Number')">
                                    <span class="truncate" title="{{ $subItem['serial_number'] }}">{{ $subItem['serial_number'] }}</span>
                                    <i class="fa-regular fa-copy text-slate-400 group-hover:text-indigo-600 ml-2"></i>
                                </div>
                            @else
                                <div class="w-full px-4 py-2.5 bg-slate-100/60 border border-slate-200/60 rounded-xl text-xs font-medium text-slate-400">
                                    N/A
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div id="noBreakdownMatches" class="hidden text-center py-8 text-slate-400 font-semibold text-xs">
                No item models found matching your search.
            </div>
        </div>
    {{-- Certification Statement --}}
    <div class="mt-8 pt-6 border-t border-slate-200">
        <div class="bg-slate-50/60 border border-slate-200 rounded-2xl p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-certificate text-sm"></i>
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-700 font-medium leading-relaxed">
                    <span class="font-extrabold text-slate-900">IT Investment Recoveries</span> certifies that the materials referenced above have been recycled in accordance with established IT Asset Management & E-Waste recycling procedures and in compliance with applicable <span class="font-extrabold text-slate-900">NIST SP 800-88 Rev. 2</span> Guidelines for Media Sanitization / Physical Destruction method used, as well as all applicable local, city, state, and federal laws and regulations.
                </p>
            </div>
        </div>
    </div>

    {{-- Authorized Signature --}}
    <div class="mt-6 pt-6 border-t border-slate-200">
        <div class="flex-1 max-w-sm">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Authorized Signature</p>
            <p class="text-2xl text-slate-800 mt-2 mb-1" style="font-family: 'Dancing Script', cursive;">Salwaster Daniel</p>
            <div class="border-b-2 border-slate-300"></div>
        </div>
    </div>

    {{-- Bottom Action Bar --}}
    <div class="mt-8 flex justify-center no-print">
        <button onclick="openPrintModal()" type="button" 
                class="inline-flex items-center gap-2.5 px-6 py-3 rounded-2xl bg-slate-900 text-white hover:bg-slate-800 font-extrabold text-sm shadow-md transition cursor-pointer">
            <i class="fa-solid fa-print"></i>
            <span>Print Full IT Asset Details</span>
        </button>
    </div>
</div>

{{-- Print Preview Modal --}}
<div id="printAssetModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 sm:p-6 overflow-y-auto">
    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden my-auto max-h-[92vh] flex flex-col">
        {{-- Modal Toolbar --}}
        <div class="flex items-center justify-between px-6 py-4 bg-slate-900 text-white border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">
                    <i class="fa-solid fa-print"></i>
                </div>
                <div>
                    <h3 class="text-base font-black">IT Asset Details Print Preview</h3>
                    <p class="text-xs text-slate-400 font-medium">Barcode #{{ $item->barcode }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="triggerAssetPrint()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>Print / Save PDF</span>
                </button>
                <button type="button" onclick="closePrintModal()" 
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-bold transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Preview Body --}}
        <div class="p-6 sm:p-8 overflow-y-auto bg-slate-100/70 flex-1">
            {{-- Printable Report Card --}}
            <div id="printableAssetReport" class="bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-xs space-y-8 font-sans">
                {{-- Branding & Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-indigo-600 text-white font-black text-sm">IT</span>
                            <span class="text-xl font-black tracking-tight text-slate-900">IT INVESTMENT RECOVERIES</span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Official IT Asset Details Report</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white {{ $badgeStyle }}">
                            Status: {{ $statusDisplay }}
                        </span>
                        <p class="text-xs text-slate-500 font-bold mt-1.5">Registered Date: {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                {{-- Barcode Banner --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Asset Barcode Number</span>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">#{{ $item->barcode }}</h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Pallet Number Ref</span>
                        <p class="text-sm font-black text-slate-900">{{ $item->pallet_number ?: 'Unassigned' }}</p>
                    </div>
                </div>

                {{-- Specifications Table --}}
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-3">1. Asset Specifications</h3>
                    <table class="w-full text-xs text-left border border-slate-200 rounded-lg overflow-hidden">
                        <tbody class="divide-y divide-slate-200 font-bold text-slate-700">
                            <tr class="bg-slate-50">
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80 w-1/4">Primary Item Name</td>
                                <td class="py-2.5 px-4 w-1/4">{{ $item->primary_name }}</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80 w-1/4">Category</td>
                                <td class="py-2.5 px-4 w-1/4">{{ $item->category ?: 'IT Assets' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Brand / Model</td>
                                <td class="py-2.5 px-4">{{ $item->primary_brand }} / {{ $item->primary_model }}</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Sub-Category</td>
                                <td class="py-2.5 px-4">{{ $item->sub_category ?: 'N/A' }}</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Total Quantity</td>
                                <td class="py-2.5 px-4 font-black text-indigo-700">{{ $item->quantity }} units</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Condition</td>
                                <td class="py-2.5 px-4">{{ $item->condition ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Total Weight</td>
                                <td class="py-2.5 px-4">{{ $item->weight ?: 0 }} lbs</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Placement Location</td>
                                <td class="py-2.5 px-4">{{ $item->item_placement ?: 'Unassigned' }}</td>
                            </tr>
                            @if ($item->notes)
                            <tr class="bg-slate-50">
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Notes / Instructions</td>
                                <td colspan="3" class="py-2.5 px-4 text-slate-800 font-medium">{{ $item->notes }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Serialized Item Models Breakdown Table --}}
                @if (!empty($item->parsed_items) && count($item->parsed_items) > 0)
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-3">2. Serialized Item Models Breakdown</h3>
                    <table class="w-full text-xs text-left border border-slate-200 rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-slate-100 text-slate-900 uppercase font-extrabold text-[10px] tracking-wider border-b border-slate-200">
                                <th class="py-2.5 px-3 w-10 text-center">#</th>
                                <th class="py-2.5 px-4">Item Name</th>
                                <th class="py-2.5 px-4">Brand</th>
                                <th class="py-2.5 px-4">Model</th>
                                <th class="py-2.5 px-4">Serial Number</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-bold text-slate-700">
                            @foreach ($item->parsed_items as $subItem)
                                <tr>
                                    <td class="py-2 px-3 text-center text-slate-400 font-extrabold">{{ $subItem['index'] }}</td>
                                    <td class="py-2 px-4 text-slate-900 font-extrabold">{{ $subItem['name'] }}</td>
                                    <td class="py-2 px-4">{{ $subItem['brand'] }}</td>
                                    <td class="py-2 px-4">{{ $subItem['model'] }}</td>
                                    <td class="py-2 px-4 font-mono text-slate-800">{{ $subItem['serial_number'] ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- Certification Statement (Print) --}}
                <div class="pt-6 border-t border-slate-200">
                    <p class="text-[10px] sm:text-xs text-slate-700 font-medium leading-relaxed">
                        <span class="font-extrabold text-slate-900">IT Investment Recoveries</span> certifies that the materials referenced above have been recycled in accordance with established IT Asset Management & E-Waste recycling procedures and in compliance with applicable <span class="font-extrabold text-slate-900">NIST SP 800-88 Rev. 2</span> Guidelines for Media Sanitization / Physical Destruction method used, as well as all applicable local, city, state, and federal laws and regulations.
                    </p>
                </div>

                {{-- Authorized Signature (Print) --}}
                <div class="pt-6 mt-6 border-t border-slate-200">
                    <div class="flex-1 max-w-xs">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Authorized Signature</p>
                        <p class="text-2xl text-slate-800 mt-2 mb-1" style="font-family: 'Dancing Script', cursive;">Salwaster Daniel</p>
                        <div class="border-b-2 border-slate-300"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        margin: 0;
    }
    header, nav, sidebar, #sidebar, #sidebarOverlay, #toastContainer, .no-print, .no-print * {
        display: none !important;
    }
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 15mm !important;
        height: auto !important;
        overflow: visible !important;
    }
    #printAssetModal {
        position: static !important;
        display: block !important;
        background: transparent !important;
        backdrop-filter: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-height: none !important;
        overflow: visible !important;
    }
    #printAssetModal > div {
        max-height: none !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    #printableAssetReport {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
}
</style>

@push('scripts')
<script>
    const breakdownSearchInput = document.getElementById('breakdownSearchInput');
    const breakdownCards = document.querySelectorAll('.breakdown-card');
    const noBreakdownMatches = document.getElementById('noBreakdownMatches');

    breakdownSearchInput?.addEventListener('input', function() {
        const query = (this.value || '').toLowerCase().trim();
        let visibleCount = 0;

        breakdownCards.forEach(card => {
            const searchText = card.dataset.search || '';
            if (!query || searchText.includes(query)) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (noBreakdownMatches) {
            noBreakdownMatches.classList.toggle('hidden', visibleCount > 0);
        }
    });

    function openPrintModal() {
        const modal = document.getElementById('printAssetModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePrintModal() {
        const modal = document.getElementById('printAssetModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function triggerAssetPrint() {
        const reportElement = document.getElementById('printableAssetReport');
        if (!reportElement) {
            window.print();
            return;
        }

        const reportHtml = reportElement.outerHTML;
        const barcodeNum = '{{ addslashes($item->barcode) }}';
        const printWin = window.open('', '_blank', 'width=1000,height=850');
        if (!printWin) {
            window.print();
            return;
        }

        let docContent = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        docContent += '<title>IT Asset Details Report - #' + barcodeNum + '</title>';
        docContent += '<script src="https://cdn.tailwindcss.com"><\/script>';
        docContent += '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
        docContent += '<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">';
        docContent += '<style>@page { margin: 0; } body { font-family: "Plus Jakarta Sans", system-ui, sans-serif; background: #ffffff; padding: 15mm; color: #0f172a; } @media print { body { padding: 12mm; } }</style>';
        docContent += '</head><body class="bg-white"><div class="max-w-4xl mx-auto">';
        docContent += reportHtml;
        docContent += '</div><script>setTimeout(function() { window.print(); }, 400);<\/script></body></html>';

        printWin.document.write(docContent);
        printWin.document.close();
    }

    document.getElementById('printAssetModal')?.addEventListener('click', function(e) {
        if (e.target.id === 'printAssetModal') {
            closePrintModal();
        }
    });
</script>
@endpush
@endsection

