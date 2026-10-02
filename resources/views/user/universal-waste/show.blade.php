@extends('user.layouts.app')

@section('title', 'Universal Waste Details - ' . ($item->pallet_number ?? $item->barcode ?? $item->id))

@section('content')
<div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-6">
    {{-- Breadcrumb & Top Action Buttons --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.universal-waste.index') }}" 
               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Universal Waste</span>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Waste Entry #{{ $item->pallet_number ?? $item->barcode ?? $item->id }}</h1>
            </div>
        </div>

        <div class="no-print">
            <button onclick="openPrintModal()" type="button" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-extrabold transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Print / Save Certificate PDF</span>
            </button>
        </div>
    </div>

    @php
        $rawStatus = strtolower(trim($item->status ?? ''));
        if (str_contains($rawStatus, 'complet') || str_contains($rawStatus, 'recycl') || str_contains($rawStatus, 'ready') || str_contains($rawStatus, 'processed')) {
            $statusDisplay = 'COMPLETED';
            $badgeStyle = 'bg-[#10b981] text-white';
        } elseif (str_contains($rawStatus, 'progr') || str_contains($rawStatus, 'process') || str_contains($rawStatus, 'pending')) {
            $statusDisplay = 'PROCESSING';
            $badgeStyle = 'bg-[#ff6b00] text-white';
        } else {
            $statusDisplay = 'RECEIVED';
            $badgeStyle = 'bg-[#357af6] text-white';
        }
    @endphp

    {{-- Top Section Grid: Specs (2 cols) & Photos (1 col at top right) --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 items-start">
        {{-- Main Specs Card --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="portal-card rounded-3xl p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-6">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h2 class="text-xl font-black text-slate-900">{{ $item->name ?? 'Universal Waste Item' }}</h2>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1.5">
                            Brand: <span class="text-slate-800 font-extrabold">{{ $item->brand ?? 'N/A' }}</span>
                            <span class="mx-2 text-slate-200">|</span>
                            Model: <span class="text-slate-800 font-extrabold">{{ $item->model ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white shadow-xs {{ $badgeStyle }}">
                            {{ $statusDisplay }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Category</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->category ?? 'Universal Waste' }}</p>
                    </div>
                    @if (isset($item->sub_category) && $item->sub_category)
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Sub-category</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->sub_category }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Quantity</p>
                        <p class="mt-1 text-lg font-black text-slate-900">{{ $item->quantity ?? 1 }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Weight</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ number_format($item->weight ?: 0, 1) }} lbs</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Pallet Ref</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-box text-slate-400"></i>
                            {{ $item->pallet_number ?? $item->barcode ?? 'Unassigned' }}
                        </p>
                    </div>
                    @if (isset($item->item_placement) && $item->item_placement)
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Placement Location</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-900">{{ $item->item_placement }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Registered Date</p>
                        <p class="mt-1 text-sm font-extrabold text-slate-900">{{ isset($item->created_at) ? date('M d, Y', strtotime($item->created_at)) : 'N/A' }}</p>
                    </div>
                </div>

                @if (isset($item->notes) && $item->notes)
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
                    if (isset($item->photos_paths) && $item->photos_paths) {
                        $photos = is_string($item->photos_paths) ? json_decode($item->photos_paths, true) : $item->photos_paths;
                    }
                @endphp

                @if (!empty($photos) && is_array($photos))
                    <div class="grid grid-cols-2 gap-3 items-start">
                        @foreach ($photos as $photo)
                            @php
                                $cleanPath = ltrim($photo, '/');
                                $isImage = str_starts_with($photo, 'data:image/') || preg_match('/\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i', strtok($photo, '?'));
                                
                                if (str_starts_with($photo, 'data:') || str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
                                    $photoSrc = $photo;
                                } elseif (file_exists(public_path($cleanPath))) {
                                    $photoSrc = asset($cleanPath);
                                } elseif (file_exists(public_path('storage/' . $cleanPath))) {
                                    $photoSrc = asset('storage/' . $cleanPath);
                                } elseif (str_starts_with($cleanPath, 'storage/')) {
                                    $photoSrc = asset($cleanPath);
                                } else {
                                    $photoSrc = asset('storage/' . $cleanPath);
                                }
                                
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
                                <div class="group relative flex flex-col justify-between h-44 w-full rounded-2xl border border-slate-200/80 bg-slate-50 p-2.5 transition hover:border-emerald-300 hover:shadow-xs">
                                    <div class="relative h-28 w-full overflow-hidden rounded-xl bg-slate-100/90 flex items-center justify-center">
                                        <img src="{{ $photoSrc }}" class="h-full w-full object-contain p-1 transition duration-300 group-hover:scale-105" alt="Photo Attachment" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'100%25\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg>';">
                                    </div>
                                    <div class="flex items-center justify-between gap-1.5 pt-2">
                                        <button type="button" onclick="window.openImageModal('{{ $photoSrc }}', '{{ addslashes($filename) }}')" 
                                                class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-eye text-[10px]"></i> View
                                        </button>
                                        <a href="{{ $photoSrc }}" download="{{ $filename }}" target="_blank" 
                                           class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-teal-50 hover:border-teal-200 hover:text-teal-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-download text-[10px]"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @else
                                @php
                                    $iconClass = 'fa-file-lines text-emerald-500';
                                    if (str_contains(strtolower($photo), 'pdf') || str_contains(strtolower($filename), 'pdf')) {
                                        $iconClass = 'fa-file-pdf text-rose-500';
                                    } elseif (str_contains(strtolower($photo), 'csv') || str_contains(strtolower($photo), 'excel') || str_contains(strtolower($filename), 'xlsx') || str_contains(strtolower($filename), 'xls')) {
                                        $iconClass = 'fa-file-excel text-emerald-500';
                                    } elseif (str_contains(strtolower($photo), 'word') || str_contains(strtolower($filename), 'doc') || str_contains(strtolower($filename), 'docx')) {
                                        $iconClass = 'fa-file-word text-blue-600';
                                    }
                                @endphp
                                <div class="group relative flex flex-col justify-between h-44 w-full rounded-2xl border border-slate-200/80 bg-slate-50/80 p-2.5 transition hover:border-emerald-300 hover:bg-slate-50 hover:shadow-xs">
                                    <div class="flex flex-col items-center justify-center h-28 w-full rounded-xl bg-white border border-slate-100 p-2 text-center">
                                        <i class="fa-solid {{ $iconClass }} text-3xl mb-1.5 transition duration-300 group-hover:scale-110"></i>
                                        <span class="text-[11px] font-extrabold text-slate-800 truncate w-full px-1" title="{{ $filename }}">{{ $filename }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1.5 pt-2">
                                        <button type="button" onclick="window.viewDocument('{{ $photoSrc }}', '{{ addslashes($filename) }}')" 
                                                class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600 text-[11px] font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-eye text-[10px]"></i> View
                                        </button>
                                        <a href="{{ $photoSrc }}" download="{{ $filename }}" 
                                           class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-teal-50 hover:border-teal-200 hover:text-teal-600 text-[11px] font-bold transition shadow-2xs">
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
                        <p class="text-slate-400 text-xs font-semibold">No photos attached for this item</p>
                    </div>
                @endif
            </div>
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
            <span>Print Official Certificate of Recycling</span>
        </button>
    </div>
</div>

{{-- Print Preview Modal --}}
<div id="printWasteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 sm:p-6 overflow-y-auto">
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden my-auto max-h-[92vh] flex flex-col">
        {{-- Modal Toolbar --}}
        <div class="flex items-center justify-between px-6 py-4 bg-slate-900 text-white border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <h3 class="text-base font-black">Certificate of Recycling Print Preview</h3>
                    <p class="text-xs text-slate-400 font-medium">Certificate #COR-UW-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="triggerWastePrint()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
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
            <div id="printableCertificateContent">
                {{-- Dynamic clone or outer element --}}
            </div>

                {{-- Certification Statement (Print) --}}
                <div class="pt-6 border-t border-slate-200">
                    <p class="text-[10px] sm:text-xs text-slate-700 font-medium leading-relaxed">
                        <span class="font-extrabold text-slate-900">IT Investment Recoveries</span> certifies that the materials referenced above have been recycled in accordance with established universal e-waste recycling procedures and in compliance with applicable <span class="font-extrabold text-slate-900">NIST SP 800-88 Rev. 2</span> Guidelines for Media Sanitization / Physical Destruction method used, as well as all applicable local, city, state, and federal laws and regulations.
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
    #certificateDocument {
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
    function openPrintModal() {
        const modal = document.getElementById('printWasteModal');
        const container = document.getElementById('printableCertificateContent');
        const certDoc = document.getElementById('certificateDocument');

        if (container && certDoc) {
            container.innerHTML = certDoc.outerHTML;
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePrintModal() {
        const modal = document.getElementById('printWasteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function triggerWastePrint() {
        const certDoc = document.getElementById('certificateDocument');
        if (!certDoc) {
            window.print();
            return;
        }

        const reportHtml = certDoc.outerHTML;
        const certId = 'COR-UW-{{ str_pad($item->id, 6, "0", STR_PAD_LEFT) }}';
        const printWin = window.open('', '_blank', 'width=1000,height=850');
        if (!printWin) {
            window.print();
            return;
        }

        let docContent = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        docContent += '<title>Certificate of Recycling - #' + certId + '</title>';
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

    document.getElementById('printWasteModal')?.addEventListener('click', function(e) {
        if (e.target.id === 'printWasteModal') {
            closePrintModal();
        }
    });
</script>
@endpush
@endsection
