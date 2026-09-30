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
        .pallet-details-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .pallet-details-grid {
                grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
            }
        }
        .pallet-main-column {
            min-width: 0;
        }
        .pallet-photos-column {
            min-width: 0;
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
            <button onclick="openPrintModal()" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 font-extrabold text-xs shadow-sm transition cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Print Intake Details</span>
            </button>
        </div>
    </div>

    <div class="pallet-details-grid grid grid-cols-1 gap-6 lg:gap-8 items-start">
        {{-- Left / Center Column (2 cols) --}}
        <div class="pallet-main-column space-y-8">
            
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


        {{-- Right Column (1 col) - Pallet Photos --}}
        <div class="pallet-photos-column">
            <div class="dashboard-card rounded-3xl border border-slate-200/80 bg-white p-6 sticky top-24">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-blue-600"></i>
                        Photos & Files
                    </h3>
                </div>

                @php
                    $photos = [];
                    if ($pallet->photos_paths) {
                        $photos = is_string($pallet->photos_paths) ? json_decode($pallet->photos_paths, true) : $pallet->photos_paths;
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
                                <div class="group relative flex flex-col justify-between h-44 w-full rounded-2xl border border-slate-200/80 bg-slate-50 p-2.5 transition hover:border-blue-300 hover:shadow-xs">
                                    <div class="relative h-28 w-full overflow-hidden rounded-xl bg-slate-100/90 flex items-center justify-center">
                                        <img src="{{ $photoSrc }}" class="h-full w-full object-contain p-1 transition duration-300 group-hover:scale-105" alt="{{ $filename }}">
                                    </div>
                                    <div class="flex items-center justify-between gap-1.5 pt-2">
                                        <button type="button" onclick="window.openImageModal('{{ $photoSrc }}', '{{ addslashes($filename) }}')" 
                                                class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-[11px] font-bold transition shadow-2xs">
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
                                    $iconClass = 'fa-file-lines text-blue-500';
                                    if (str_contains(strtolower($photo), 'pdf') || str_contains(strtolower($filename), 'pdf')) {
                                        $iconClass = 'fa-file-pdf text-red-500';
                                    } elseif (str_contains(strtolower($photo), 'csv') || str_contains(strtolower($photo), 'excel') || str_contains(strtolower($filename), 'xlsx') || str_contains(strtolower($filename), 'xls')) {
                                        $iconClass = 'fa-file-excel text-emerald-500';
                                    } elseif (str_contains(strtolower($photo), 'word') || str_contains(strtolower($filename), 'doc') || str_contains(strtolower($filename), 'docx')) {
                                        $iconClass = 'fa-file-word text-blue-600';
                                    }
                                @endphp
                                <div class="group relative flex flex-col justify-between h-44 w-full rounded-2xl border border-slate-200/80 bg-slate-50/80 p-2.5 transition hover:border-blue-300 hover:bg-slate-50 hover:shadow-xs">
                                    <div class="flex flex-col items-center justify-center h-28 w-full rounded-xl bg-white border border-slate-100 p-2 text-center">
                                        <i class="fa-solid {{ $iconClass }} text-3xl mb-1.5 transition duration-300 group-hover:scale-110"></i>
                                        <span class="text-[11px] font-extrabold text-slate-800 truncate w-full px-1" title="{{ $filename }}">{{ $filename }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1.5 pt-2">
                                        <button type="button" onclick="window.viewDocument('{{ $photoSrc }}', '{{ addslashes($filename) }}')" 
                                                class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 px-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 text-[11px] font-bold transition shadow-2xs">
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
                        <i class="fa-regular fa-image text-slate-300 text-3xl mb-2"></i>
                        <p class="text-slate-400 text-xs font-bold">No photos uploaded for this pallet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Action Bar --}}
    <div class="mt-8 flex justify-center no-print">
        <button onclick="openPrintModal()" type="button" 
                class="inline-flex items-center gap-2.5 px-6 py-3 rounded-2xl bg-slate-900 text-white hover:bg-slate-800 font-extrabold text-sm shadow-md transition cursor-pointer">
            <i class="fa-solid fa-print"></i>
            <span>Print Full Intake Details</span>
        </button>
    </div>
</div>

{{-- Print Preview Modal --}}
<div id="printIntakeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 sm:p-6 overflow-y-auto">
    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden my-auto max-h-[92vh] flex flex-col">
        {{-- Modal Toolbar --}}
        <div class="flex items-center justify-between px-6 py-4 bg-slate-900 text-white border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white font-bold">
                    <i class="fa-solid fa-print"></i>
                </div>
                <div>
                    <h3 class="text-base font-black">Intake Details Print Preview</h3>
                    <p class="text-xs text-slate-400 font-medium">Pallet Barcode #{{ $pallet->barcode_number }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="triggerIntakePrint()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs transition shadow-sm cursor-pointer">
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
            <div id="printableIntakeReport" class="bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-xs space-y-8 font-sans">
                {{-- Branding & Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-600 text-white font-black text-sm">IT</span>
                            <span class="text-xl font-black tracking-tight text-slate-900">IT INVESTMENT RECOVERIES</span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Official Received Intake Report</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider text-white {{ $statusClass }}">
                            Status: {{ $pallet->status ?: 'Received' }}
                        </span>
                        <p class="text-xs text-slate-500 font-bold mt-1.5">Intake Date: {{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                {{-- Barcode Banner --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Pallet Barcode Number</span>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">#{{ $pallet->display_barcode ?? $pallet->barcode_number }}</h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Location Tag</span>
                        <p class="text-sm font-black text-slate-900">{{ $pallet->put_away_location ?: 'Unassigned' }}</p>
                    </div>
                </div>

                {{-- Specifications Table --}}
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-3">1. Pallet Specifications</h3>
                    <table class="w-full text-xs text-left border border-slate-200 rounded-lg overflow-hidden">
                        <tbody class="divide-y divide-slate-200 font-bold text-slate-700">
                            <tr class="bg-slate-50">
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80 w-1/4">Put Away Location</td>
                                <td class="py-2.5 px-4 w-1/4">{{ $pallet->put_away_location ?: 'Unassigned' }}</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80 w-1/4">Estimated Item Count</td>
                                <td class="py-2.5 px-4 w-1/4">{{ number_format($pallet->estimated_count) }} units</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Gross Weight</td>
                                <td class="py-2.5 px-4">{{ number_format($pallet->gross_weight, 1) }} lbs</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Tare Weight</td>
                                <td class="py-2.5 px-4">{{ number_format($pallet->tare_weight, 1) }} lbs</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Net Weight</td>
                                <td class="py-2.5 px-4 font-black text-blue-700">{{ number_format($pallet->gross_weight - $pallet->tare_weight, 1) }} lbs</td>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Marketing Reference</td>
                                <td class="py-2.5 px-4">{{ $pallet->marketing_reference ?: 'N/A' }}</td>
                            </tr>
                            @if ($pallet->notes)
                            <tr>
                                <td class="py-2.5 px-4 font-extrabold text-slate-900 bg-slate-100/80">Pallet Notes</td>
                                <td colspan="3" class="py-2.5 px-4 text-slate-800 font-medium">{{ $pallet->notes }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
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
    #printIntakeModal {
        position: static !important;
        display: block !important;
        background: transparent !important;
        backdrop-filter: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-height: none !important;
        overflow: visible !important;
    }
    #printIntakeModal > div {
        max-height: none !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    #printableIntakeReport {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
}
</style>

<script>
    function openPrintModal() {
        const modal = document.getElementById('printIntakeModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePrintModal() {
        const modal = document.getElementById('printIntakeModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function triggerIntakePrint() {
        const reportElement = document.getElementById('printableIntakeReport');
        if (!reportElement) {
            window.print();
            return;
        }

        const reportHtml = reportElement.outerHTML;
        const barcodeNum = '{{ addslashes($pallet->barcode_number) }}';
        const printWin = window.open('', '_blank', 'width=1000,height=850');
        if (!printWin) {
            window.print();
            return;
        }

        let docContent = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        docContent += '<title>Intake Details Report - #' + barcodeNum + '</title>';
        docContent += '<script src="https://cdn.tailwindcss.com"><\/script>';
        docContent += '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
        docContent += '<style>@page { margin: 0; } body { font-family: "Plus Jakarta Sans", system-ui, sans-serif; background: #ffffff; padding: 15mm; color: #0f172a; } @media print { body { padding: 12mm; } }</style>';
        docContent += '</head><body class="bg-white"><div class="max-w-4xl mx-auto">';
        docContent += reportHtml;
        docContent += '</div><script>setTimeout(function() { window.print(); }, 400);<\/script></body></html>';

        printWin.document.write(docContent);
        printWin.document.close();
    }

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

    function toggleBreakdown(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.toggle('hidden');
        }
    }

    document.getElementById('printIntakeModal')?.addEventListener('click', function(e) {
        if (e.target.id === 'printIntakeModal') {
            closePrintModal();
        }
    });
</script>
@endsection

