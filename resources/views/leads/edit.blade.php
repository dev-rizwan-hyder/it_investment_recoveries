@extends('leads.layout')

@section('title', 'Edit Lead')
@section('page-title', 'Edit Lead')

@section('header-action')
    <a href="{{ route('summary.leads.show', $lead) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
        <i class="fa-solid fa-arrow-left"></i>
        Lead Details
    </a>
@endsection

@section('content')
    @include('leads._form', [
        'action' => route('summary.leads.update', $lead),
        'method' => 'PUT',
        'submitLabel' => 'Update Lead',
        'cancelUrl' => route('summary.leads.show', $lead),
    ])
@endsection
