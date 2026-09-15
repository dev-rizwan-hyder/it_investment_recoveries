@extends('user.layouts.app')

@section('title', 'Intake Details - ' . $pallet->barcode_number)

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 30px rgba(15, 23, 42, 0.04);
        }
        .tab-btn.active {
            border-bottom-color: #2563eb;
            color: #2563eb;
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    {{-- Breadcrumb & Back --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('user.received-intake.index') }}"
           class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Received Intake</span>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Pallet #{{ $pallet->barcode_number }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Pallet Specs --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                <h2 class="text-lg font-black text-slate-950 mb-5">Pallet Specifications</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Status</p>
                        <p class="mt-1">
                            @php
                                $statusClass = match(strtolower($pallet->status)) {
                                    'received' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'processing' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100'
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                {{ $pallet->status ?: 'Received' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Put Away Location</p>
                        <p class="mt-1 text-slate-900 font-extrabold flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                            {{ $pallet->put_away_location ?: 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Received Date</p>
                        <p class="mt-1 text-slate-900 font-extrabold">
                            {{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Estimated Item Count</p>
                        <p class="mt-1 text-slate-900 font-extrabold text-lg">
                            {{ $pallet->estimated_count }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Gross Weight</p>
                        <p class="mt-1 text-slate-900 font-extrabold text-lg">
                            {{ number_format($pallet->gross_weight, 1) }} lbs
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Tare Weight</p>
                        <p class="mt-1 text-slate-900 font-extrabold text-lg">
                            {{ number_format($pallet->tare_weight, 1) }} lbs
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Net Weight</p>
                        <p class="mt-1 text-blue-600 font-black text-lg">
                            {{ number_format($pallet->gross_weight - $pallet->tare_weight, 1) }} lbs
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Marketing Reference</p>
                        <p class="mt-1 text-slate-900 font-extrabold">
                            {{ $pallet->marketing_reference ?: 'N/A' }}
                        </p>
                    </div>
                </div>

                @if ($pallet->notes)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <p class="text-xs font-bold text-slate-400 uppercase">Notes</p>
                        <p class="mt-2 text-slate-600 text-sm leading-relaxed font-medium bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            {{ $pallet->notes }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Shipment Breakdown (Recycling Intake Details) --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                <h2 class="text-lg font-black text-slate-950 mb-3">Shipment Items Breakdown</h2>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-6">Received details from Recycling Intake</p>
                
                <div class="space-y-6">
                    @forelse ($relatedPallets as $index => $item)
                        <div class="p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition">
                            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100/80 pb-3 mb-4">
                                <div class="min-w-0">
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-blue-100 text-blue-700 uppercase mb-1">
                                        Intake Item #{{ $index + 1 }}
                                    </span>
                                    <h3 class="font-extrabold text-slate-900 text-sm leading-snug">{{ $item->description }}</h3>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Put Away Location</span>
                                    <span class="text-xs font-extrabold text-slate-700"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $item->put_away_location ?: 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                                <div class="bg-white p-2 rounded-xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Estimated Qty</span>
                                    <span class="text-sm font-black text-slate-800">{{ $item->estimated_count ?: 0 }}</span>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Gross Weight</span>
                                    <span class="text-sm font-extrabold text-slate-700">{{ number_format($item->gross_weight, 1) }} lbs</span>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Tare Weight</span>
                                    <span class="text-sm font-extrabold text-slate-700">{{ number_format($item->tare_weight, 1) }} lbs</span>
                                </div>
                                <div class="bg-blue-50/50 p-2 rounded-xl border border-blue-100/60">
                                    <span class="block text-[10px] font-black text-blue-600 uppercase">Net Weight</span>
                                    <span class="text-sm font-black text-blue-700">{{ number_format($item->gross_weight - $item->tare_weight, 1) }} lbs</span>
                                </div>
                            </div>

                            @if ($item->notes)
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Item Notes</span>
                                    <p class="text-xs text-slate-600 font-medium leading-relaxed bg-white p-3 rounded-xl border border-slate-100">
                                        {{ $item->notes }}
                                    </p>
                                </div>
                            @endif

                            {{-- Item-specific Photos --}}
                            @php
                                $itemPhotos = [];
                                if ($item->photos_paths) {
                                    $itemPhotos = is_string($item->photos_paths) ? json_decode($item->photos_paths, true) : $item->photos_paths;
                                }
                            @endphp
                            @if (!empty($itemPhotos) && is_array($itemPhotos))
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-2">Item Photos</span>
                                    <div class="flex flex-wrap gap-2">
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
                                                <a href="{{ $photoSrc }}" target="_blank" class="group relative block w-16 h-16 overflow-hidden rounded-xl border border-slate-200 bg-white hover:border-blue-200 transition">
                                                    <img src="{{ $photoSrc }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Item photo">
                                                </a>
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
                                                <a href="{{ $photoSrc }}" target="_blank" download="{{ $filename }}" class="group relative flex flex-col items-center justify-center w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 p-1 text-center hover:border-blue-300 transition" title="{{ $filename }}">
                                                    <i class="fa-solid {{ $iconClass }} text-xl mb-0.5"></i>
                                                    <span class="text-[8px] font-bold text-slate-700 truncate w-full px-0.5">{{ $filename }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-center py-10 text-slate-400 font-medium">No items found in this shipment.</p>
                    @endforelse
                </div>
            </div>

            {{-- Processed Items Tabs --}}
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 pt-5">
                    <h3 class="text-lg font-black text-slate-950 mb-3">Processed Items</h3>
                    <div class="flex gap-4 border-b border-slate-200 overflow-x-auto">
                        <button onclick="switchTab('refurbishing')" id="tab-btn-refurbishing" class="tab-btn active px-4 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-950 transition-colors">
                            Refurbishing ({{ $refurbishingItems->count() }})
                        </button>
                        <button onclick="switchTab('ecommerce')" id="tab-btn-ecommerce" class="tab-btn px-4 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-950 transition-colors">
                            E-commerce ({{ $ecommerceItems->count() }})
                        </button>
                        <button onclick="switchTab('universal')" id="tab-btn-universal" class="tab-btn px-4 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-950 transition-colors">
                            Universal Waste ({{ $universalWasteItems->count() }})
                        </button>
                        <button onclick="switchTab('destruction')" id="tab-btn-destruction" class="tab-btn px-4 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-950 transition-colors">
                            Data Destruction ({{ $dataDestructionItems->count() }})
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Refurbishing Tab --}}
                    <div id="tab-content-refurbishing" class="tab-content space-y-4">
                        @forelse ($refurbishingItems as $item)
                            <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 border border-slate-100 rounded-2xl hover:bg-slate-50/30 transition">
                                <div>
                                    <h4 class="font-extrabold text-slate-900">{{ $item->name }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">
                                        <span class="font-bold text-slate-500">Barcode:</span> {{ $item->barcode }}
                                        <span class="mx-1.5 text-slate-200">|</span>
                                        <span class="font-bold text-slate-500">Category:</span> {{ $item->category }}
                                        @if ($item->serial_number)
                                            <span class="mx-1.5 text-slate-200">|</span>
                                            <span class="font-bold text-slate-500">Serial:</span> {{ $item->serial_number }}
                                        @endif
                                    </p>
                                    @if ($item->notes)
                                        <p class="text-xs text-slate-500 bg-slate-50/80 px-3 py-2 rounded-xl mt-2 font-medium border border-slate-100/50">
                                            {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 sm:justify-end shrink-0">
                                    <span class="inline-flex flex-col text-right">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Qty</span>
                                        <span class="text-slate-900 font-extrabold">{{ $item->quantity }}</span>
                                    </span>
                                    <span class="inline-flex flex-col text-right ml-4">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Condition</span>
                                        <span class="text-slate-900 font-extrabold">{{ $item->condition }}</span>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center py-10 text-slate-400 font-medium">No refurbishing items processed yet.</p>
                        @endforelse
                    </div>

                    {{-- E-commerce Tab --}}
                    <div id="tab-content-ecommerce" class="tab-content hidden space-y-4">
                        @forelse ($ecommerceItems as $item)
                            <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 border border-slate-100 rounded-2xl hover:bg-slate-50/30 transition">
                                <div>
                                    <h4 class="font-extrabold text-slate-900">{{ $item->name }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">
                                        <span class="font-bold text-slate-500">Barcode:</span> {{ $item->barcode }}
                                        <span class="mx-1.5 text-slate-200">|</span>
                                        <span class="font-bold text-slate-500">Category:</span> {{ $item->category }}
                                        @if ($item->serial_number)
                                            <span class="mx-1.5 text-slate-200">|</span>
                                            <span class="font-bold text-slate-500">Serial:</span> {{ $item->serial_number }}
                                        @endif
                                    </p>
                                    @if ($item->notes)
                                        <p class="text-xs text-slate-500 bg-slate-50/80 px-3 py-2 rounded-xl mt-2 font-medium border border-slate-100/50">
                                            {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 sm:justify-end shrink-0">
                                    <span class="inline-flex flex-col text-right">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Qty</span>
                                        <span class="text-slate-900 font-extrabold">{{ $item->quantity }}</span>
                                    </span>
                                    <span class="inline-flex flex-col text-right ml-4">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Condition</span>
                                        <span class="text-slate-900 font-extrabold">{{ $item->condition }}</span>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center py-10 text-slate-400 font-medium">No e-commerce items processed yet.</p>
                        @endforelse
                    </div>

                    {{-- Universal Waste Tab --}}
                    <div id="tab-content-universal" class="tab-content hidden space-y-4">
                        @forelse ($universalWasteItems as $item)
                            <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 border border-slate-100 rounded-2xl hover:bg-slate-50/30 transition">
                                <div>
                                    <h4 class="font-extrabold text-slate-900">{{ $item->name }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">
                                        <span class="font-bold text-slate-500">Barcode:</span> {{ $item->barcode }}
                                        <span class="mx-1.5 text-slate-200">|</span>
                                        <span class="font-bold text-slate-500">Category:</span> {{ $item->category }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 sm:justify-end shrink-0">
                                    <span class="inline-flex flex-col text-right">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Qty</span>
                                        <span class="text-slate-900 font-extrabold">{{ $item->quantity }}</span>
                                    </span>
                                    <span class="inline-flex flex-col text-right ml-4">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Weight</span>
                                        <span class="text-slate-900 font-extrabold">{{ number_format($item->weight, 1) }} lbs</span>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center py-10 text-slate-400 font-medium">No universal waste items processed yet.</p>
                        @endforelse
                    </div>

                    {{-- Data Destruction Tab --}}
                    <div id="tab-content-destruction" class="tab-content hidden space-y-4">
                        @forelse ($dataDestructionItems as $item)
                            <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 border border-slate-100 rounded-2xl hover:bg-slate-50/30 transition">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-slate-900">{{ $item->name }}</h4>
                                        @php
                                            $badgeClass = match(strtolower($item->status)) {
                                                'completed' => 'bg-emerald-100 text-emerald-700',
                                                'in progress' => 'bg-blue-100 text-blue-700',
                                                'ready for inventory' => 'bg-indigo-100 text-indigo-700',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">
                                        <span class="font-bold text-slate-500">Barcode:</span> {{ $item->barcode }}
                                        <span class="mx-1.5 text-slate-200">|</span>
                                        <span class="font-bold text-slate-500">Category:</span> {{ $item->category }}
                                    </p>
                                    @if ($item->serial_number)
                                        <p class="text-xs text-slate-500 mt-2 font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-100/50">
                                            <span class="font-bold text-slate-400 block mb-0.5 text-[10px] uppercase">Serials:</span>
                                            {{ $item->serial_number }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 sm:justify-end shrink-0">
                                    <span class="inline-flex flex-col text-right">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Qty</span>
                                        <span class="text-slate-900 font-extrabold">{{ $item->quantity }}</span>
                                    </span>
                                    <span class="inline-flex flex-col text-right ml-4">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Destruction</span>
                                        <span class="text-red-600 font-black">Wiping</span>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center py-10 text-slate-400 font-medium">No data destruction items processed yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Pallet Photos --}}
        <div class="lg:col-span-1">
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6">
                <h3 class="text-base font-black text-slate-950 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-images text-slate-400"></i>
                    Pallet Photos
                </h3>

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
                                <a href="{{ $photoSrc }}" target="_blank" class="group relative block aspect-square overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 hover:border-blue-200 transition">
                                    <img src="{{ $photoSrc }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Pallet photo">
                                    <span class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <i class="fa-solid fa-up-right-from-square text-white text-sm"></i>
                                    </span>
                                </a>
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
                                <a href="{{ $photoSrc }}" target="_blank" download="{{ $filename }}" class="group relative flex flex-col items-center justify-center aspect-square rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center hover:border-blue-300 hover:bg-slate-100/80 transition">
                                    <i class="fa-solid {{ $iconClass }} text-3xl mb-2 transition duration-300 group-hover:scale-110"></i>
                                    <span class="text-[10px] font-bold text-slate-700 truncate w-full px-1" title="{{ $filename }}">{{ $filename }}</span>
                                    <span class="mt-1 text-[9px] font-extrabold uppercase text-slate-400 group-hover:text-blue-600">Download</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-regular fa-image text-slate-300 text-2xl mb-2"></i>
                        <p class="text-slate-400 text-xs font-semibold">No photos available for this pallet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.add('hidden');
        });
        
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-btn').forEach(function(el) {
            el.classList.remove('active');
        });
        
        // Show current tab content
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');
        
        // Activate current tab button
        document.getElementById('tab-btn-' + tabId).classList.add('active');
    }
</script>
@endsection
