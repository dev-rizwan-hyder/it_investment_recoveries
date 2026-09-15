@extends('user.layouts.app')

@section('title', 'Received Intake')

@push('styles')
    <style>
        .dashboard-card {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-received {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        {{-- Header Section --}}
        <section
            class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-8 text-white shadow-xl sm:px-8 lg:px-10 lg:py-10">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/3 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-blue-300">Recycling Tracking</p>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Received Intake
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300 sm:text-base">
                        Track the processing status, item counts, and net weights of all shipments received from your
                        company.
                    </p>
                </div>
                @if ($client)
                    <div class="shrink-0">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-300">
                            <i class="fa-solid fa-user-tie"></i>
                            Linked to: {{ $client->name }} ({{ $client->company ?: 'No Company' }})
                        </span>
                    </div>
                @endif
            </div>
        </section>

        {{-- Table Section --}}
        <section class="mt-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200/80 overflow-hidden">
                @if ($pallets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead>
                                <tr
                                    class="text-[10px] uppercase font-black text-slate-400 tracking-[0.2em] bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-8 py-6">Pallet Barcode #</th>
                                    <th class="px-8 py-6">Status</th>
                                    <th class="px-8 py-6 text-center">Items (Total / Remaining)</th>
                                    <th class="px-8 py-6 text-center">Weight (Total / Remaining)</th>
                                    <th class="px-8 py-6">Location</th>
                                    <th class="px-8 py-6">Description</th>
                                    <th class="px-8 py-6">Received Date</th>
                                    <th class="px-8 py-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-bold text-slate-700 divide-y divide-slate-50">
                                @foreach ($pallets as $pallet)
                                    @php
                                        $statusClass = match (strtolower($pallet->status)) {
                                            'received' => 'status-received',
                                            'processing' => 'status-processing',
                                            'completed' => 'status-completed',
                                            default => 'status-received',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <span
                                                class="font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                                                <i class="fa-solid fa-box text-slate-400"></i>
                                                {{ $pallet->barcode_number }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $pallet->status ?: 'Received' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="text-slate-900">{{ $pallet->estimated_count }}</span>
                                            <span class="text-slate-300 mx-1">/</span>
                                            <span
                                                class="text-slate-500 font-medium">{{ $pallet->remaining_quantity }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span
                                                class="text-slate-900">{{ number_format($pallet->gross_weight - $pallet->tare_weight, 1) }}
                                                lbs</span>
                                            <span class="text-slate-300 mx-1">/</span>
                                            <span
                                                class="text-slate-500 font-medium">{{ number_format($pallet->remaining_weight, 1) }}
                                                lbs</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span
                                                class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                                {{ $pallet->put_away_location ?: 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <p class="max-w-[200px] truncate text-slate-500 font-medium"
                                                title="{{ $pallet->description }}">
                                                {{ $pallet->description }}
                                            </p>
                                        </td>
                                        <td class="px-8 py-5 text-slate-500 font-medium">
                                            {{ $pallet->created_at ? $pallet->created_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('user.received-intake.show', $pallet->id) }}"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-blue-600">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                                View Items
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($pallets->hasPages())
                        <div class="px-8 py-5 border-t border-slate-100">
                            {{ $pallets->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-20">
                        <div
                            class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                            <i class="fa-solid fa-boxes-stacked text-slate-300 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900">No received intake found</h3>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
