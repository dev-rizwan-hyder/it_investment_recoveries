@extends('leads.layout')

@section('title', 'Lead Summary')
@section('page-title', 'Lead Summary')

@section('header-action')
    <a href="{{ route('summary.leads.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700">
        <i class="fa-solid fa-plus"></i>
        Add Lead
    </a>
@endsection

@section('content')
@php
    $badgeClasses = [
        'New' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Contacted' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'Follow Up' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Qualified' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'Converted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Lost' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];
@endphp

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Total</p>
        <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $stats['total'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">New</p>
        <p class="mt-2 text-2xl font-extrabold text-blue-700">{{ $stats['new'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Contacted</p>
        <p class="mt-2 text-2xl font-extrabold text-cyan-700">{{ $stats['contacted'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Follow Up</p>
        <p class="mt-2 text-2xl font-extrabold text-amber-700">{{ $stats['follow_up'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Converted</p>
        <p class="mt-2 text-2xl font-extrabold text-emerald-700">{{ $stats['converted'] }}</p>
    </div>
</div>

<form method="GET" action="{{ route('summary.leads.index') }}" class="mt-5 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search leads"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>
        <input type="text" name="status" value="{{ request('status') }}" placeholder="Status"
            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">
            <i class="fa-solid fa-filter"></i>
            Filter
        </button>
    </div>
</form>

<div class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-left">
            <thead class="bg-slate-50">
                <tr class="text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">Lead</th>
                    <th class="px-4 py-3">Company</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Updated</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($leads as $lead)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-4">
                            <div class="font-extrabold text-slate-950">{{ $lead->full_name ?: $lead->first_name }}</div>
                            <div class="mt-1 max-w-xs truncate text-xs font-semibold text-slate-500">{{ $lead->address ?: 'No address' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-slate-700">{{ $lead->company_name ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            <div>{{ $lead->email ?: '-' }}</div>
                            <div class="text-xs font-semibold text-slate-500">{{ $lead->phone ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-slate-700">{{ $lead->location ?: '-' }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $badgeClasses[$lead->status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                {{ $lead->status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs font-semibold text-slate-500">{{ $lead->updated_at->format('M d, Y') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('summary.leads.show', $lead) }}" title="View lead" aria-label="View lead" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ route('summary.leads.edit', $lead) }}" title="Edit lead" aria-label="Edit lead" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('summary.leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete lead" aria-label="Delete lead" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                <i class="fa-regular fa-address-book"></i>
                            </div>
                            <p class="mt-3 text-sm font-bold text-slate-700">No leads found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($leads->hasPages())
        <div class="border-t border-slate-100 px-4 py-3">
            {{ $leads->links() }}
        </div>
    @endif
</div>
@endsection
