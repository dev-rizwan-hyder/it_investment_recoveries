@extends('layouts.admin')

@section('title', 'Policy Records')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-100 p-8">
        <div class="max-w-8xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex mb-6 items-center gap-2">
                <a href="#" class="text-slate-600 hover:text-slate-900 transition flex items-center gap-2">
                    <i class="fas fa-home text-blue-600"></i>
                    Dashboard
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-900 font-semibold flex items-center gap-2">
                    <i class="fas fa-file-contract text-blue-600"></i>
                    Policy Records
                </span>
            </nav>

            <!-- Header Section with Stats -->
            <div class="mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-6">
                    <div>
                        <h1 class="text-5xl font-black text-slate-900 mb-2">Policies Directory</h1>
                        <p class="text-slate-600 text-lg">Create and manage company policies, procedures, and refurbishing guidelines</p>
                    </div>
                    <a href="{{ route('admin.policies.create') }}"
                        class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all font-bold text-lg">
                        <i class="fas fa-plus-circle"></i>
                        New Policy
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-blue-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Total Policies</p>
                                <p class="text-3xl font-black text-slate-900 mt-2">{{ $policies->total() }}</p>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                                <i class="fas fa-layer-group text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-emerald-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">With Images</p>
                                <p class="text-3xl font-black text-slate-900 mt-2">
                                    {{ $policies->where('image', '!=', null)->count() }}</p>
                            </div>
                            <div
                                class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                                <i class="fas fa-image text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-amber-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">This Month</p>
                                <p class="text-3xl font-black text-slate-900 mt-2">
                                    {{ $policies->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                            </div>
                            <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div
                    class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search & Filter Controls -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-8 no-print">
                <form action="{{ route('admin.policies.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
                    @if($type)
                        <input type="hidden" name="type" value="{{ $type }}">
                    @endif
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-base"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search ?? '' }}"
                            placeholder="Search by ID, title, category, department, purpose or scope..." 
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition text-sm font-medium text-slate-900 placeholder-slate-400"
                        >
                        @if(!empty($search))
                            <a href="{{ route('admin.policies.index', array_filter(['type' => $type])) }}" 
                               class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-500 transition">
                                <i class="fas fa-times-circle text-base"></i>
                            </a>
                        @endif
                    </div>
                    <div class="flex gap-3 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition font-bold text-sm flex items-center justify-center gap-2 shadow-sm">
                            Search
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('admin.policies.index', array_filter(['type' => $type])) }}" class="flex-1 md:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl transition font-bold text-sm flex items-center justify-center gap-2">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Category Tabs Navigation -->
            <div class="mb-8 border-b border-slate-200">
                <div class="flex flex-wrap -mb-px text-sm font-semibold gap-2">
                    <a href="{{ route('admin.policies.index', array_filter(['search' => $search])) }}" 
                       class="inline-flex items-center gap-2 px-5 py-3 border-b-2 rounded-t-lg transition-all duration-200 {{ empty($type) ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                        <i class="fas fa-list"></i> All Policies
                    </a>
                    <a href="{{ route('admin.policies.index', array_filter(['type' => 'company', 'search' => $search])) }}" 
                       class="inline-flex items-center gap-2 px-5 py-3 border-b-2 rounded-t-lg transition-all duration-200 {{ in_array($type, ['company', 'company_policy']) ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                        <i class="fas fa-building"></i> Company Policy
                    </a>
                    <a href="{{ route('admin.policies.index', array_filter(['type' => 'procedure', 'search' => $search])) }}" 
                       class="inline-flex items-center gap-2 px-5 py-3 border-b-2 rounded-t-lg transition-all duration-200 {{ in_array($type, ['procedure', 'company_procedure', 'procedures']) ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                        <i class="fas fa-tasks"></i> Company Procedure
                    </a>
                    <a href="{{ route('admin.policies.index', array_filter(['type' => 'policy_procedure', 'search' => $search])) }}" 
                       class="inline-flex items-center gap-2 px-5 py-3 border-b-2 rounded-t-lg transition-all duration-200 {{ in_array($type, ['policy_procedure', 'company_policy_procedure', 'refurbishing']) ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                        <i class="fas fa-file-contract"></i> Company Policy & Procedure
                    </a>
                </div>
            </div>

            <!-- Cards Grid or Table View -->
            @forelse ($policies as $policy)
                @if ($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif
                <div class="group relative bg-white rounded-2xl border border-slate-200 hover:border-slate-300 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <!-- Top Accent Line or Image Header -->
                    @php
                        $accentColor = match($policy->type) {
                            'Company Policy' => 'bg-indigo-600',
                            'Company Procedure' => 'bg-emerald-600',
                            'Company Policy & Procedure' => 'bg-purple-600',
                            default => 'bg-slate-400'
                        };
                    @endphp
                    
                    @if ($policy->image)
                        <div class="relative h-36 overflow-hidden bg-slate-100 border-b border-slate-100">
                            <img src="{{ url('files/' . $policy->image) }}" alt="{{ $policy->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                            <div class="absolute top-3 right-3 bg-black/60 text-white px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide">
                                Flowchart
                            </div>
                        </div>
                    @else
                        <div class="h-1.5 w-full {{ $accentColor }}"></div>
                    @endif

                    <!-- Card Body -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Header Row with Type & ID -->
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <span class="text-xs uppercase font-extrabold tracking-wider text-slate-400">
                                    {{ $policy->type ?? 'Policy' }}
                                </span>
                                <span class="bg-slate-100 text-slate-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                    ID: {{ $policy->policy_id ?? 'N/A' }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition line-clamp-2 leading-snug">
                                <a href="{{ route('admin.policies.show', $policy->id) }}">
                                    {{ $policy->name }}
                                </a>
                            </h3>

                            <!-- Category & Department Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @if($policy->category)
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md text-[11px] font-semibold border border-blue-100">
                                        <i class="fas fa-tag text-[9px]"></i>
                                        {{ $policy->category }}
                                    </span>
                                @endif
                                @if($policy->department)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md text-[11px] font-semibold border border-emerald-100">
                                        <i class="fas fa-building text-[9px]"></i>
                                        {{ $policy->department }}
                                    </span>
                                @endif
                            </div>

                            <!-- Description/Purpose Preview -->
                            <p class="text-sm text-slate-600 leading-relaxed line-clamp-3 mb-6">
                                @if(!empty($policy->sections) && is_array($policy->sections) && isset($policy->sections[0]['content']))
                                    {{ Str::limit($policy->sections[0]['content'], 150) }}
                                @else
                                    {{ $policy->purpose ?? ($policy->reason ?? 'No description provided.') }}
                                @endif
                            </p>
                        </div>

                        <!-- Footer Area -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                            <!-- Effective Date / Metadata -->
                            <span class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="far fa-calendar-alt"></i>
                                @if($policy->effective_date)
                                    Effective: {{ \Carbon\Carbon::parse($policy->effective_date)->format('M d, Y') }}
                                @else
                                    Created: {{ $policy->created_at->format('M d, Y') }}
                                @endif
                            </span>

                            <!-- Actions -->
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.policies.show', $policy->id) }}" 
                                   class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                   title="View Policy Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.policies.edit', $policy->id) }}" 
                                   class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                   title="Edit Policy">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button onclick="confirmDelete({{ $policy->id }})" 
                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Delete Policy">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                                <form id="delete-form-{{ $policy->id }}"
                                      action="{{ route('admin.policies.destroy', $policy->id) }}" method="POST"
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($loop->last)
                    </div>
                @endif
    @empty
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-16 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 rounded-full mb-6">
                <i class="fas fa-layer-group text-4xl text-blue-600"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">No Policies Yet</h3>
            <p class="text-slate-600 text-lg mb-8">Get started by creating your first policy record</p>
            <a href="{{ route('admin.policies.create') }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-2xl hover:shadow-lg transition-all font-bold">
                <i class="fas fa-plus-circle"></i>
                Create First Policy
            </a>
        </div>
        @endforelse

        <!-- Pagination -->
        @if ($policies->hasPages())
            <div class="mt-8 flex flex-col items-center justify-between gap-4">
                <p class="text-sm text-slate-600 font-semibold">
                    Showing <span class="text-blue-600 font-black">{{ $policies->firstItem() }}</span> to
                    <span class="text-blue-600 font-black">{{ $policies->lastItem() }}</span> of
                    <span class="text-blue-600 font-black">{{ $policies->total() }}</span> policies
                </p>
                <div class="flex justify-center">
                    {{ $policies->links() }}
                </div>
            </div>
        @endif
    </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <script>
        function confirmDelete(id) {
            const element = event.target.closest('button');
            const policyName = element.closest('.bg-white').querySelector('h3').textContent.trim();

            if (confirm(`Are you sure you want to delete "${policyName}"? This action cannot be undone.`)) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
