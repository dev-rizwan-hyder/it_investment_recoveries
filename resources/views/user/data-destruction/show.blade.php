@extends('user.layouts.app')

@section('title', 'Compliance Details - ' . $item->barcode)

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-6">
    {{-- Breadcrumb & Back --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('user.data-destruction.index') }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Data Destruction</span>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Compliance Entry #{{ $item->barcode }}</h1>
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
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                    Contains {{ $item->total_parsed_count }} Media Items
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
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->category }}</p>
                    </div>
                    @if ($item->sub_category)
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Sub-category</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->sub_category }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Quantity</p>
                        <p class="mt-1 text-lg font-black text-slate-900">{{ $item->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Sanitized Qty</p>
                        <p class="mt-1 text-lg font-black text-emerald-600">{{ $item->reuse_quantity }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Scrapped Qty</p>
                        <p class="mt-1 text-lg font-black text-rose-500">{{ $item->scrap_quantity }}</p>
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

        {{-- Photos & Files Column (Top Right Side) --}}
        <div class="lg:col-span-1">
            <div class="portal-card rounded-3xl p-6">
                <h3 class="text-sm font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-images text-slate-400"></i>
                    Hardware Photos & Attachments
                </h3>

                @php
                    $photos = [];
                    if ($item->photos_paths) {
                        $photos = is_string($item->photos_paths) ? json_decode($item->photos_paths, true) : $item->photos_paths;
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
                                     class="group relative block aspect-square max-h-36 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 hover:border-rose-300 transition cursor-pointer shadow-2xs">
                                    <img src="{{ $photoSrc }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Hardware photo">
                                    <span class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                        <i class="fa-solid fa-expand"></i> View
                                    </span>
                                </div>
                            @else
                                <a href="{{ $photoSrc }}" target="_blank" download="{{ $filename }}" class="group relative flex flex-col items-center justify-center aspect-square max-h-36 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center hover:border-rose-300 hover:bg-slate-100/80 transition shadow-2xs">
                                    <i class="fa-solid fa-file-pdf text-3xl text-rose-500 mb-2 transition duration-300 group-hover:scale-110"></i>
                                    <span class="text-[10px] font-bold text-slate-700 truncate w-full px-1" title="{{ $filename }}">{{ $filename }}</span>
                                    <span class="mt-1 text-[9px] font-extrabold uppercase text-slate-400 group-hover:text-rose-600">Download</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-regular fa-image text-slate-300 text-2xl mb-2"></i>
                        <p class="text-slate-400 text-xs font-semibold">No photos attached for this item</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Section: Serialized Media Devices Breakdown (Full Width) --}}
    <div class="portal-card rounded-3xl p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-black text-slate-900">Serialized Media Devices Breakdown</h3>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                        {{ count($item->parsed_items) }} {{ Str::plural('Media Item', count($item->parsed_items)) }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Serialized storage media list for sanitization compliance</p>
            </div>

            @if(count($item->parsed_items) > 3)
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           id="breakdownSearchInput" 
                           placeholder="Filter media items..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-100 transition">
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
                                <div class="flex items-center justify-between w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 shadow-2xs group cursor-pointer hover:border-rose-300 transition"
                                     onclick="window.copyToClipboard('{{ $subItem['serial_number'] }}', 'Serial Number')">
                                    <span class="truncate" title="{{ $subItem['serial_number'] }}">{{ $subItem['serial_number'] }}</span>
                                    <i class="fa-regular fa-copy text-slate-400 group-hover:text-rose-600 ml-2"></i>
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
                No media items found matching your search.
            </div>
        </div>
    </div>
</div>
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
</script>
@endpush
@endsection

