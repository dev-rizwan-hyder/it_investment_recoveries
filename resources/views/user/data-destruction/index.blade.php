@extends('user.layouts.app')

@section('title', 'Data Destruction')

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

        .status-wiping {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-ready {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
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
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-red-500/10 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/3 h-64 w-64 rounded-full bg-violet-500/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-red-300">Security & compliance</p>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Data Destruction
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300 sm:text-base">
                        Track the serialization and certified wiping progress of hard drives, solid state drives, and device
                        memory.
                    </p>
                </div>
                @if ($client)
                    <div class="shrink-0">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-red-300">
                            <i class="fa-solid fa-shield-halved"></i>
                            Linked to: {{ $client->name }}
                        </span>
                    </div>
                @endif
            </div>
        </section>

        {{-- Table Section --}}
        <section class="mt-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200/80 overflow-hidden">
                @if ($items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead>
                                <tr
                                    class="text-[10px] uppercase font-black text-slate-400 tracking-[0.2em] bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-8 py-6">Barcode #</th>
                                    <th class="px-8 py-6">Device Name</th>
                                    <th class="px-8 py-6">Brand / Model</th>
                                    <th class="px-8 py-6">Category</th>
                                    <th class="px-8 py-6 text-center">Status</th>
                                    <th class="px-8 py-6 text-center">Qty / Weight</th>
                                    <th class="px-8 py-6">Serial Numbers</th>
                                    <th class="px-8 py-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-bold text-slate-700 divide-y divide-slate-50">
                                @foreach ($items as $item)
                                    @php
                                        $statusClass = match (strtolower($item->status)) {
                                            'in progress' => 'status-wiping',
                                            'ready for inventory' => 'status-ready',
                                            'completed' => 'status-completed',
                                            default => 'status-pending',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <span
                                                class="font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                                                <i class="fa-solid fa-hard-drive text-slate-400"></i>
                                                {{ $item->barcode }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-slate-900 font-extrabold">
                                            {{ $item->name }}
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-slate-700">
                                                {{ $item->brand ?: 'N/A' }} / {{ $item->model ?: 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-slate-500 font-medium">
                                            {{ $item->category }}
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $item->status ?: 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center font-medium">
                                            <span class="text-slate-900 font-bold">{{ $item->quantity }}</span>
                                            <span class="text-slate-300 mx-1">|</span>
                                            <span class="text-slate-500">{{ $item->weight ?: 0 }} lbs</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <p class="max-w-[200px] truncate text-slate-500 font-mono text-xs font-semibold"
                                                title="{{ $item->serial_number }}">
                                                {{ $item->serial_number ?: 'N/A' }}
                                            </p>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('user.data-destruction.show', $item->id) }}"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-red-600">
                                                <i class="fa-solid fa-shield-halved text-xs"></i>
                                                Compliance Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($items->hasPages())
                        <div class="px-8 py-5 border-t border-slate-100">
                            {{ $items->links() }}
                        </div>
                    @endif
                @elseif ($noClientLinked ?? false)
                    <div class="text-center py-20 px-8">
                        <div
                            class="bg-yellow-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5 border-2 border-yellow-200">
                            <i class="fa-solid fa-link-slash text-yellow-600 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900">No Client Profile Linked</h3>
                        <p class="mt-2 text-slate-600 max-w-md mx-auto">
                            Your account ({{ $userEmail }}) is not linked to a client profile in our system. 
                            Please contact support to link your account to your organization's profile.
                        </p>
                        <a href="{{ Route::has('contact') ? route('contact') : url('/contact-us') }}" 
                           class="mt-6 inline-flex items-center gap-2 rounded-xl bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 font-bold transition">
                            <i class="fa-solid fa-envelope"></i>
                            Contact Support
                        </a>
                    </div>
                @else
                    <div class="text-center py-20">
                        <div
                            class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                            <i class="fa-solid fa-shield-slash text-slate-300 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900">No data destruction found</h3>
                        <p class="mt-2 text-slate-600">
                            There are no data destruction items for your organization at this time.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
