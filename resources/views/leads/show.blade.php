@extends('leads.layout')

@section('title', 'Lead Details')
@section('page-title', 'Lead Details')

@section('header-action')
    <div class="flex items-center gap-2">
        <a href="{{ route('summary.leads.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i>
            Leads
        </a>
        <a href="{{ route('summary.leads.edit', $lead) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700">
            <i class="fa-regular fa-pen-to-square"></i>
            Edit
        </a>
    </div>
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

<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-950">{{ $lead->full_name ?: $lead->first_name }}</h2>
            <p class="mt-1 text-sm font-semibold text-slate-500">{{ $lead->company_name ?: 'No company' }}</p>
        </div>
        <span class="inline-flex w-fit rounded-full border px-3 py-1.5 text-sm font-bold {{ $badgeClasses[$lead->status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
            {{ $lead->status }}
        </span>
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-2">
        <div class="rounded-lg bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Email</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ $lead->email ?: '-' }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Phone</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ $lead->phone ?: '-' }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Location</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ $lead->location ?: '-' }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Created</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ $lead->created_at->format('M d, Y h:i A') }}</p>
        </div>
    </div>

    <div class="mt-4 rounded-lg bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Address</p>
        <p class="mt-2 whitespace-pre-line text-sm font-semibold leading-6 text-slate-800">{{ $lead->address ?: '-' }}</p>
    </div>

    <div class="mt-4 rounded-lg bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Remarks</p>
        <p class="mt-2 whitespace-pre-line text-sm font-semibold leading-6 text-slate-800">{{ $lead->remarks ?: '-' }}</p>
    </div>

    <div class="mt-5 flex justify-end">
        <form method="POST" action="{{ route('summary.leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-100">
                <i class="fa-regular fa-trash-can"></i>
                Delete Lead
            </button>
        </form>
    </div>
</div>
@endsection
