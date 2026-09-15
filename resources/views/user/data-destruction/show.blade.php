@extends('user.layouts.app')

@section('title', 'Compliance Details - ' . $item->barcode)

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 30px rgba(15, 23, 42, 0.04);
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    {{-- Breadcrumb & Back --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('user.data-destruction.index') }}"
           class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Data Destruction</span>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Compliance Entry #{{ $item->barcode }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Specs --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-5">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">{{ $item->name }}</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">
                            Brand: <span class="text-slate-800 font-extrabold">{{ $item->brand ?: 'N/A' }}</span>
                            <span class="mx-2 text-slate-200">|</span>
                            Model: <span class="text-slate-800 font-extrabold">{{ $item->model ?: 'N/A' }}</span>
                        </p>
                    </div>
                    <div>
                        @php
                            $badgeClass = match(strtolower($item->status)) {
                                'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'in progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'ready for inventory' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold border uppercase tracking-wider {{ $badgeClass }}">
                            {{ $item->status ?: 'Pending' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Category</p>
                        <p class="mt-1 text-slate-900 font-extrabold">{{ $item->category }}</p>
                    </div>
                    @if ($item->sub_category)
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Sub-category</p>
                            <p class="mt-1 text-slate-900 font-extrabold">{{ $item->sub_category }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Total Quantity</p>
                        <p class="mt-1 text-slate-900 font-black text-lg">{{ $item->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Wiped Quantity</p>
                        <p class="mt-1 text-emerald-600 font-black text-lg">{{ $item->reuse_quantity }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Scrapped Quantity</p>
                        <p class="mt-1 text-red-500 font-black text-lg">{{ $item->scrap_quantity }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Total Weight</p>
                        <p class="mt-1 text-slate-900 font-extrabold">{{ $item->weight ?: 0 }} lbs</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Pallet Reference</p>
                        <p class="mt-1 text-slate-900 font-extrabold flex items-center gap-1.5">
                            <i class="fa-solid fa-box text-slate-400"></i>
                            {{ $item->pallet_number ?: 'Unassigned' }}
                        </p>
                    </div>
                    @if ($item->item_placement)
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Item Placement</p>
                            <p class="mt-1 text-slate-900 font-extrabold">{{ $item->item_placement }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Registered Date</p>
                        <p class="mt-1 text-slate-900 font-extrabold">{{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                {{-- Serial Numbers --}}
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Logged Serial Numbers</h3>
                    @if ($item->serial_number)
                        @php
                            // Check if serial numbers are comma/newline separated
                            $serials = preg_split('/[\n\r,]+/', $item->serial_number);
                            $serials = array_filter(array_map('trim', $serials));
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach ($serials as $serial)
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold font-mono text-slate-700">
                                    <i class="fa-solid fa-barcode text-slate-400"></i>
                                    {{ $serial }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 font-medium">No individual serial numbers logged for this entry.</p>
                    @endif
                </div>

                @if ($item->notes)
                    <div class="mt-8 border-t border-slate-100 pt-6">
                        <p class="text-xs font-bold text-slate-400 uppercase">Notes & Special Instructions</p>
                        <p class="mt-2 text-slate-600 text-sm leading-relaxed font-medium bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            {{ $item->notes }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Destruction Compliance Certificate Mockup --}}
            @if (strtolower($item->status) === 'completed')
                <div class="dashboard-card rounded-3xl border border-emerald-200 bg-emerald-50/20 p-6 flex flex-col sm:flex-row items-center gap-5">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-3xl text-emerald-600">
                        <i class="fa-solid fa-file-shield"></i>
                    </span>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="font-extrabold text-emerald-900 text-base">Certificate of Data Destruction Generated</h3>
                        <p class="text-xs text-emerald-700 mt-1 leading-relaxed">
                            This device has undergone complete data sanitization. All persistent memory has been securely wiped or physically destroyed in compliance with industrial security standards.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm shadow-emerald-500/10">
                            <i class="fa-solid fa-check"></i> Wiped & Verified
                        </span>
                    </div>
                </div>
            @else
                <div class="dashboard-card rounded-3xl border border-slate-200 bg-slate-50 p-6 flex flex-col sm:flex-row items-center gap-5">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-3xl text-slate-400">
                        <i class="fa-solid fa-spinner animate-spin text-slate-400"></i>
                    </span>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="font-extrabold text-slate-800 text-base">Data Sanitization in Progress</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Secured data sanitization operations are currently active. A Certificate of Data Destruction will be generated automatically once all items are successfully wiped.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-1 rounded-xl bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700">
                            Pending Finalization
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Photos --}}
        <div class="lg:col-span-1">
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6">
                <h3 class="text-base font-black text-slate-950 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-images text-slate-400"></i>
                    Hardware Photos
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
                                <a href="{{ $photoSrc }}" target="_blank" class="group relative block aspect-square overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 hover:border-blue-200 transition">
                                    <img src="{{ $photoSrc }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Hardware photo">
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
                        <p class="text-slate-400 text-xs font-semibold">No photos or files logged for this item</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
