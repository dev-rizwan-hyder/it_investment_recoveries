@extends('user.layouts.app')

@section('title', 'Certificate of Recycling - ' . ($item->pallet_number ?? $item->barcode ?? 'Detail'))

@section('content')
<div class="mx-auto max-w-[1200px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8 space-y-6">
    {{-- Top Navigation & Action Buttons --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <a href="{{ route('user.universal-waste.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Universal Waste / Certificates
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                <i class="fa-solid fa-print text-slate-500"></i> Print Certificate
            </button>
        </div>
    </div>

    {{-- Official Certificate Document Card --}}
    <div class="portal-card rounded-3xl p-6 sm:p-10 bg-white border border-slate-200 shadow-2xl relative overflow-hidden" id="certificateDocument">
        <!-- Certificate Watermark / Background Styling -->
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>

        <!-- Certificate Outer Frame Border -->
        <div class="border-4 border-double border-emerald-800/20 p-6 sm:p-8 rounded-2xl relative z-10 space-y-8">
            <!-- Header Seal & Title -->
            <div class="text-center space-y-4 border-b border-slate-200 pb-6">
                <div class="flex items-center justify-center gap-3">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/30">
                        <i class="fa-solid fa-award text-2xl"></i>
                    </span>
                </div>
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                        EPA Compliant Zero-Landfill Recycling
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 tracking-tight">
                        CERTIFICATE OF RECYCLING
                    </h1>
                    <p class="text-xs text-slate-500 font-semibold mt-1 uppercase tracking-wider">
                        IT Investment Recoveries • Environmental Services Division
                    </p>
                </div>
            </div>

            <!-- Certificate Body Statement -->
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">This Certifies That</p>
                <p class="text-xl font-black text-slate-900 border-b-2 border-slate-200 pb-1 inline-block px-4">
                    {{ $client->company ?: ($client->name ?? 'Valued Client Partner') }}
                </p>
                <p class="text-xs text-slate-600 leading-relaxed font-medium pt-2">
                    Has successfully surrendered electronic waste and universal waste inventory for environmentally responsible processing, material recovery, and zero-landfill disposal under federal EPA and state environmental standards.
                </p>
            </div>

            <!-- Detailed Specifications Grid -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-6 space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-200/60 pb-2">
                    Recycling & Processing Manifest Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Certificate No.</span>
                        <span class="font-mono font-black text-slate-900 text-sm">COR-UW-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Pallet / Manifest Barcode</span>
                        <span class="font-mono font-bold text-emerald-700 text-sm">{{ $item->pallet_number ?? $item->barcode ?? 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Processing Date</span>
                        <span class="font-bold text-slate-900">{{ $item->created_at ? date('F d, Y', strtotime($item->created_at)) : date('F d, Y') }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Equipment Description</span>
                        <span class="font-bold text-slate-900">{{ $item->name ?? 'Universal Waste Electronics' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Brand / Model</span>
                        <span class="font-bold text-slate-900">{{ $item->brand ?? 'Generic' }} / {{ $item->model ?? 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Category</span>
                        <span class="font-bold text-slate-900">{{ $item->category ?? 'Universal Waste' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Total Quantity</span>
                        <span class="font-bold text-slate-900">{{ $item->quantity ?? 1 }} unit(s)</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Total Recycled Weight</span>
                        <span class="font-bold text-emerald-700 text-sm">{{ number_format($item->weight ?: 0, 1) }} lbs</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Processing Status</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> {{ $item->status ?? 'Recycled' }}
                        </span>
                    </div>
                </div>

                @if ($item->notes)
                    <div class="border-t border-slate-200/60 pt-3 text-xs">
                        <span class="text-slate-400 font-extrabold block uppercase tracking-wider text-[10px]">Special Compliance Notes</span>
                        <p class="text-slate-700 font-medium italic mt-0.5">{{ $item->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Footer Signatures & Stamp -->
            <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 border border-emerald-300 text-emerald-700">
                        <i class="fa-solid fa-shield-check text-xl"></i>
                    </span>
                    <div>
                        <p class="text-xs font-black text-slate-900">VERIFIED ENVIRONMENTAL RECYCLER</p>
                        <p class="text-[10px] text-slate-500 font-semibold">100% Zero-Landfill Guarantee • EPA Guidelines Met</p>
                    </div>
                </div>

                <div class="text-center sm:text-right border-t sm:border-t-0 border-slate-200 pt-4 sm:pt-0">
                    <div class="font-signature font-black text-slate-800 text-lg tracking-wide border-b border-slate-300 pb-1 inline-block">
                        IT Investment Recoveries Ops
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Authorized Operations Manager</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
