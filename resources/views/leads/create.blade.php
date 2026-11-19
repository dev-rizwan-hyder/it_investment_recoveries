@extends('leads.layout')

@section('title', 'Add Lead')
@section('page-title', 'Add Lead')

@section('header-action')
    <a href="{{ route('summary.leads.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
        <i class="fa-solid fa-arrow-left"></i>
        Leads
    </a>
@endsection

@section('content')
    @include('leads._form', [
        'action' => route('summary.leads.store'),
        'method' => 'POST',
        'submitLabel' => 'Save Lead',
        'cancelUrl' => route('summary.leads.index'),
    ])
@endsection
