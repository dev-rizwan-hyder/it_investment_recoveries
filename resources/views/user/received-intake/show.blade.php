@extends('user.layouts.app')

@section('title', 'Intake Details - ' . $pallet->barcode_number)

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 10px 30px rgba(15, 23, 42, 0.04);
        }
        .tab-btn {
            position: relative;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tab-btn.active {
            color: #2563eb;
            font-weight: 800;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 9999px 9999px 0 0;
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    {{-- Header & Navigation --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.received-intake.index') }}"
               class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200/80 bg-white text-slate-600 shadow-sm transition hover:border-blue-300 hover:bg-slate-50 hover:text-blue-600">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Received Intake</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-500">{{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex items-center gap-3 mt-0.5">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pallet #{{ $pallet->barcode_number }}</h1>
                    <button onclick="copyToClipboard('{{ $pallet->barcode_number }}', 'Barcode #{{ $pallet->barcode_number }} copied to clipboard!')"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200/80 transition" title="Click to Copy Barcode">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copy</span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @php
                $statusClass = match(strtolower(trim($pallet->status ?: 'received'))) {
                    'received', 'pending' => 'bg-[#357af6] text-white',
                    'processing', 'in processing' => 'bg-[#ff6b00] text-white',
                    'completed' => 'bg-[#10b981] text-white',
                    default => 'bg-[#357af6] text-white'
                };
            @endphp
            <div class="inline-block px-4 py-2 rounded-full shadow-xs font-black text-xs uppercase tracking-wider text-white {{ $statusClass }}">
                Status: {{ $pallet->status ?: 'Received' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Left / Center Column (2 cols) --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Pallet Specifications Card --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 font-bold">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Pallet Specifications</h2>
                            <p class="text-xs font-medium text-slate-400">Technical dimensions and location tracking</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Location</p>
                        <p class="mt-1 text-sm font-black text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500 text-xs"></i>
                            {{ $pallet->put_away_location ?: 'Unassigned' }}
                        </p>
                    </div>
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Est. Item Count</p>
                        <p class="mt-1 text-base font-black text-slate-900">
                            {{ number_format($pallet->estimated_count) }} <span class="text-xs text-slate-400 font-medium">units</span>
                        </p>
                    </div>
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Gross Weight</p>
                        <p class="mt-1 text-base font-black text-slate-800">
                            {{ number_format($pallet->gross_weight, 1) }} <span class="text-xs text-slate-400 font-medium">lbs</span>
                        </p>
                    </div>
                    <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100/80">
                        <p class="text-[11px] font-black text-blue-600 uppercase tracking-wider">Net Weight</p>
                        <p class="mt-1 text-base font-black text-blue-700">
                            {{ number_format($pallet->gross_weight - $pallet->tare_weight, 1) }} <span class="text-xs text-blue-500 font-medium">lbs</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div class="p-3.5 rounded-2xl border border-slate-100 bg-white">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Tare Weight</span>
                        <span class="text-sm font-extrabold text-slate-700">{{ number_format($pallet->tare_weight, 1) }} lbs</span>
                    </div>
                    <div class="p-3.5 rounded-2xl border border-slate-100 bg-white">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Marketing Reference</span>
                        <span class="text-sm font-extrabold text-slate-800">{{ $pallet->marketing_reference ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl border border-slate-100 bg-white">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Received Date</span>
                        <span class="text-sm font-extrabold text-slate-800">{{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>

                @if ($pallet->notes)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Pallet Notes</p>
                        <div class="text-slate-700 text-sm leading-relaxed font-medium bg-slate-50/80 p-4 rounded-2xl border border-slate-200/60">
                            {{ $pallet->notes }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Shipment Items Breakdown --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Shipment Items Breakdown</h2>
                        <p class="text-xs font-semibold text-slate-400">Items received from initial recycling intake</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                        {{ $relatedPallets->count() }} {{ Str::plural('Item', $relatedPallets->count()) }}
                    </span>
                </div>
                
                <div class="space-y-5">
                    @forelse ($relatedPallets as $index => $item)
                        <div class="p-5 rounded-2xl border border-slate-200/70 bg-slate-50/40 hover:bg-slate-50 transition duration-200">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/60 pb-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center h-7 w-7 rounded-xl text-xs font-black bg-blue-100 text-blue-700">
                                        #{{ $index + 1 }}
                                    </span>
                                    <h3 class="font-extrabold text-slate-900 text-base">{{ $item->description }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-slate-500 bg-white px-3 py-1 rounded-xl border border-slate-200 shadow-2xs">
                                        <i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i>{{ $item->put_away_location ?: 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase">Est. Qty</span>
                                    <span class="text-base font-black text-slate-900">{{ $item->estimated_count ?: 0 }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase">Gross Weight</span>
                                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($item->gross_weight, 1) }} lbs</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase">Tare Weight</span>
                                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($item->tare_weight, 1) }} lbs</span>
                                </div>
                                <div class="bg-blue-50/80 p-3 rounded-xl border border-blue-100 shadow-2xs">
                                    <span class="block text-[10px] font-black text-blue-600 uppercase">Net Weight</span>
                                    <span class="text-sm font-black text-blue-700">{{ number_format($item->gross_weight - $item->tare_weight, 1) }} lbs</span>
                                </div>
                            </div>

                            @if ($item->notes)
                                <div class="mt-4 pt-3 border-t border-slate-200/60">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-1">Item Notes</span>
                                    <p class="text-xs text-slate-700 font-medium leading-relaxed bg-white p-3 rounded-xl border border-slate-200/60">
                                        {{ $item->notes }}
                                    </p>
                                </div>
                            @endif

                            {{-- Item Photos / Attachments --}}
                            @php
                                $itemPhotos = [];
                                if ($item->photos_paths) {
                                    $itemPhotos = is_string($item->photos_paths) ? json_decode($item->photos_paths, true) : $item->photos_paths;
                                }
                            @endphp
                            @if (!empty($itemPhotos) && is_array($itemPhotos))
                                <div class="mt-4 pt-3 border-t border-slate-200/60">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase block mb-2">Item Photos & Attachments</span>
                                    <div class="flex flex-wrap gap-2.5">
                                        @foreach ($itemPhotos as $photo)
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
                                                <div onclick="window.openImageModal('{{ $photoSrc }}', '{{ $filename }}')" 
                                                     class="group relative block w-16 h-16 overflow-hidden rounded-xl border border-slate-200 bg-white hover:border-blue-300 transition shadow-2xs cursor-pointer">
                                                    <img src="{{ $photoSrc }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Item photo">
                                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs">
                                                        <i class="fa-solid fa-expand"></i>
                                                    </div>
                                                </div>
                                            @else
                                                @php
                                                    $iconClass = 'fa-file-lines text-blue-500';
                                                    if (str_contains(strtolower($photo), 'pdf') || str_contains(strtolower($filename), 'pdf')) {
                                                        $iconClass = 'fa-file-pdf text-red-500';
                                                    } elseif (str_contains(strtolower($photo), 'csv') || str_contains(strtolower($photo), 'excel') || str_contains(strtolower($filename), 'xlsx') || str_contains(strtolower($filename), 'xls')) {
                                                        $iconClass = 'fa-file-excel text-emerald-500';
                                                    } elseif (str_contains(strtolower($photo), 'word') || str_contains(strtolower($filename), 'doc') || str_contains(strtolower($filename), 'docx')) {
                                                        $iconClass = 'fa-file-word text-blue-600';
                                                    }
                                                @endphp
                                                <a href="{{ $photoSrc }}" target="_blank" download="{{ $filename }}" class="group relative flex flex-col items-center justify-center w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 p-1 text-center hover:border-blue-300 transition shadow-2xs" title="{{ $filename }}">
                                                    <i class="fa-solid {{ $iconClass }} text-xl mb-0.5"></i>
                                                    <span class="text-[8px] font-extrabold text-slate-700 truncate w-full px-0.5">{{ $filename }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <i class="fa-solid fa-inbox text-slate-300 text-3xl mb-3"></i>
                            <p class="text-slate-500 font-bold text-sm">No shipment breakdown items found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Processed Items Tabs --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white overflow-hidden">
                <div class="border-b border-slate-200/80 bg-slate-50/60 px-6 pt-5">
                    <h3 class="text-lg font-black text-slate-950 mb-4">Processed Categorization</h3>
                    <div class="flex gap-2 border-b border-slate-200/80 overflow-x-auto no-scrollbar">
                        <button onclick="switchTab('refurbishing')" id="tab-btn-refurbishing" class="tab-btn active px-4 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-transparent text-slate-500 hover:text-slate-900 transition-colors whitespace-nowrap">
                            Refurbishing <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">{{ $refurbishingItems->count() }}</span>
                        </button>
                        <button onclick="switchTab('ecommerce')" id="tab-btn-ecommerce" class="tab-btn px-4 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-transparent text-slate-500 hover:text-slate-900 transition-colors whitespace-nowrap">
                            E-commerce <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700">{{ $ecommerceItems->count() }}</span>
                        </button>
                        <button onclick="switchTab('universal')" id="tab-btn-universal" class="tab-btn px-4 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-transparent text-slate-500 hover:text-slate-900 transition-colors whitespace-nowrap">
                            Universal Waste <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-700">{{ $universalWasteItems->count() }}</span>
                        </button>
                        <button onclick="switchTab('destruction')" id="tab-btn-destruction" class="tab-btn px-4 py-3 text-xs sm:text-sm font-extrabold border-b-2 border-transparent text-slate-500 hover:text-slate-900 transition-colors whitespace-nowrap">
                            Data Destruction <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700">{{ $dataDestructionItems->count() }}</span>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Refurbishing Tab --}}
                    <div id="tab-content-refurbishing" class="tab-content space-y-4">
                        @forelse ($refurbishingItems as $item)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 border border-slate-200/70 rounded-2xl hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-black text-slate-900 text-sm">{{ $item->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase">Refurbish</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap font-medium">
                                        <span><strong class="text-slate-700">Barcode:</strong> {{ $item->barcode }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span><strong class="text-slate-700">Category:</strong> {{ $item->category }}</span>
                                        @if ($item->serial_number)
                                            <span class="text-slate-300">•</span>
                                            <span class="inline-flex items-center gap-1 font-mono bg-slate-100 px-2 py-0.5 rounded text-[11px] text-slate-700 font-bold">
                                                SN: {{ $item->serial_number }}
                                                <button onclick="copyToClipboard('{{ $item->serial_number }}', 'Serial copied!')" class="text-slate-400 hover:text-blue-600"><i class="fa-regular fa-copy text-[10px]"></i></button>
                                            </span>
                                        @endif
                                    </div>
                                    @if ($item->notes)
                                        <p class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-xl mt-2 font-medium border border-slate-100">
                                            {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 shrink-0 bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl w-full sm:w-auto justify-between sm:justify-end border sm:border-0 border-slate-100">
                                    <div class="text-right">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Quantity</span>
                                        <span class="text-sm font-black text-slate-900">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="text-right pl-3 border-l border-slate-200/80">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Condition</span>
                                        <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-100">{{ $item->condition }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 font-semibold text-sm">No refurbishing items processed yet.</div>
                        @endforelse
                    </div>

                    {{-- E-commerce Tab --}}
                    <div id="tab-content-ecommerce" class="tab-content hidden space-y-4">
                        @forelse ($ecommerceItems as $item)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 border border-slate-200/70 rounded-2xl hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-black text-slate-900 text-sm">{{ $item->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">E-Commerce</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap font-medium">
                                        <span><strong class="text-slate-700">Barcode:</strong> {{ $item->barcode }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span><strong class="text-slate-700">Category:</strong> {{ $item->category }}</span>
                                        @if ($item->serial_number)
                                            <span class="text-slate-300">•</span>
                                            <span class="inline-flex items-center gap-1 font-mono bg-slate-100 px-2 py-0.5 rounded text-[11px] text-slate-700 font-bold">
                                                SN: {{ $item->serial_number }}
                                                <button onclick="copyToClipboard('{{ $item->serial_number }}', 'Serial copied!')" class="text-slate-400 hover:text-blue-600"><i class="fa-regular fa-copy text-[10px]"></i></button>
                                            </span>
                                        @endif
                                    </div>
                                    @if ($item->notes)
                                        <p class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-xl mt-2 font-medium border border-slate-100">
                                            {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 shrink-0 bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl w-full sm:w-auto justify-between sm:justify-end border sm:border-0 border-slate-100">
                                    <div class="text-right">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Quantity</span>
                                        <span class="text-sm font-black text-slate-900">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="text-right pl-3 border-l border-slate-200/80">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Condition</span>
                                        <span class="text-xs font-black text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-100">{{ $item->condition }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 font-semibold text-sm">No e-commerce items processed yet.</div>
                        @endforelse
                    </div>

                    {{-- Universal Waste Tab --}}
                    <div id="tab-content-universal" class="tab-content hidden space-y-4">
                        @forelse ($universalWasteItems as $item)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 border border-slate-200/70 rounded-2xl hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-black text-slate-900 text-sm">{{ $item->name }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 uppercase">Recycle Waste</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap font-medium">
                                        <span><strong class="text-slate-700">Barcode:</strong> {{ $item->barcode }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span><strong class="text-slate-700">Category:</strong> {{ $item->category }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 shrink-0 bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl w-full sm:w-auto justify-between sm:justify-end border sm:border-0 border-slate-100">
                                    <div class="text-right">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Qty</span>
                                        <span class="text-sm font-black text-slate-900">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="text-right pl-3 border-l border-slate-200/80">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Weight</span>
                                        <span class="text-xs font-black text-slate-800 bg-slate-100 px-2.5 py-0.5 rounded-md">{{ number_format($item->weight, 1) }} lbs</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 font-semibold text-sm">No universal waste items processed yet.</div>
                        @endforelse
                    </div>

                    {{-- Data Destruction Tab --}}
                    <div id="tab-content-destruction" class="tab-content hidden space-y-4">
                        @forelse ($dataDestructionItems as $item)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 border border-slate-200/70 rounded-2xl hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-black text-slate-900 text-sm">{{ $item->name }}</h4>
                                        @php
                                            $badgeClass = match(strtolower($item->status)) {
                                                'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'in progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'ready for inventory' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $badgeClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap font-medium">
                                        <span><strong class="text-slate-700">Barcode:</strong> {{ $item->barcode }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span><strong class="text-slate-700">Category:</strong> {{ $item->category }}</span>
                                    </div>
                                    @if ($item->serial_number)
                                        <div class="mt-2 font-mono text-[11px] bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/60 text-slate-700 flex items-center justify-between">
                                            <span><strong>Serial:</strong> {{ $item->serial_number }}</span>
                                            <button onclick="copyToClipboard('{{ $item->serial_number }}', 'Serial copied!')" class="text-slate-400 hover:text-blue-600 ml-2">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 shrink-0 bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl w-full sm:w-auto justify-between sm:justify-end border sm:border-0 border-slate-100">
                                    <div class="text-right">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Quantity</span>
                                        <span class="text-sm font-black text-slate-900">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="text-right pl-3 border-l border-slate-200/80">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Sanitization</span>
                                        <span class="text-xs font-black text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-md border border-rose-100">Data Sanitized</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 font-semibold text-sm">No data destruction items processed yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column (1 col) - Pallet Photos --}}
        <div class="lg:col-span-1">
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sticky top-24">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-blue-600"></i>
                        Pallet Photos & Docs
                    </h3>
                </div>

                @php
                    $photos = [];
                    if ($pallet->photos_paths) {
                        $photos = is_string($pallet->photos_paths) ? json_decode($pallet->photos_paths, true) : $pallet->photos_paths;
                    }
                @endphp

                @if (!empty($photos) && is_array($photos))
                    <div class="grid grid-cols-2 gap-3">
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
                                <div onclick="window.openImageModal('{{ $photoSrc }}', '{{ $filename }}')" 
                                     class="group relative block aspect-square max-h-36 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 hover:border-blue-300 transition cursor-pointer shadow-2xs">
                                    <img src="{{ $photoSrc }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Pallet photo">
                                    <span class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                        <i class="fa-solid fa-expand"></i> View
                                    </span>
                                </div>
                            @else
                                @php
                                    $iconClass = 'fa-file-lines text-blue-500';
                                    if (str_contains(strtolower($photo), 'pdf') || str_contains(strtolower($filename), 'pdf')) {
                                        $iconClass = 'fa-file-pdf text-red-500';
                                    } elseif (str_contains(strtolower($photo), 'csv') || str_contains(strtolower($photo), 'excel') || str_contains(strtolower($filename), 'xlsx') || str_contains(strtolower($filename), 'xls')) {
                                        $iconClass = 'fa-file-excel text-emerald-500';
                                    } elseif (str_contains(strtolower($photo), 'word') || str_contains(strtolower($filename), 'doc') || str_contains(strtolower($filename), 'docx')) {
                                        $iconClass = 'fa-file-word text-blue-600';
                                    }
                                @endphp
                                <a href="{{ $photoSrc }}" target="_blank" download="{{ $filename }}" class="group relative flex flex-col items-center justify-center aspect-square rounded-2xl border border-slate-200 bg-slate-50/80 p-3 text-center hover:border-blue-300 hover:bg-slate-100 transition shadow-2xs">
                                    <i class="fa-solid {{ $iconClass }} text-3xl mb-2 transition duration-300 group-hover:scale-110"></i>
                                    <span class="text-[10px] font-extrabold text-slate-700 truncate w-full px-1" title="{{ $filename }}">{{ $filename }}</span>
                                    <span class="mt-1 text-[9px] font-black uppercase text-slate-400 group-hover:text-blue-600">Download</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-regular fa-image text-slate-300 text-3xl mb-2"></i>
                        <p class="text-slate-400 text-xs font-bold">No photos uploaded for this pallet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.add('hidden');
        });
        document.querySelectorAll('.tab-btn').forEach(function(el) {
            el.classList.remove('active');
        });
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');
        document.getElementById('tab-btn-' + tabId).classList.add('active');
    }
</script>
@endsection

