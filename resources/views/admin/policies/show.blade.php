@extends('layouts.admin')

@section('title', 'View Policy Record')

@push('styles')
<style>
    @media print {
        /* Hide everything else */
        nav, header, aside, .lg\:ml-68, .no-print {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
        }
        body, main {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .lg\:ml-68 {
            margin-left: 0 !important;
        }
        
        /* Show print area and force visible rules */
        #policyPrintArea, #policyPrintArea * {
            visibility: visible !important;
        }
        #policyPrintArea {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        /* Custom print formatting */
        .print-title {
            font-size: 24pt !important;
            font-weight: 800 !important;
            text-align: center !important;
            margin-top: 10pt !important;
        }
        .print-subtitle {
            font-size: 14pt !important;
            font-weight: 700 !important;
            text-align: center !important;
            color: #475569 !important;
            margin-bottom: 20pt !important;
        }
        .print-section-header {
            font-size: 14pt !important;
            font-weight: 700 !important;
            border-bottom: 1px solid #cbd5e1 !important;
            padding-bottom: 4pt !important;
            margin-top: 15pt !important;
            margin-bottom: 8pt !important;
        }
        .print-body {
            font-size: 11pt !important;
            line-height: 1.6 !important;
            color: black !important;
        }
        .print-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 15pt !important;
        }
        
        @page {
            size: A4;
            margin: 20mm;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-8">
    <div class="max-w-8xl mx-auto">
        <!-- Breadcrumb (No Print) -->
        <nav class="flex mb-8 no-print">
            <a href="{{ route('admin.policies.index') }}" class="text-slate-700 hover:text-black transition">Policy Records</a>
            <span class="text-slate-400 mx-3">/</span>
            <span class="text-slate-900 font-semibold">Details</span>
        </nav>

        <!-- Header with Back Button (No Print) -->
        <div class="flex justify-between items-center mb-8 no-print">
            <div>
                <h1 class="text-4xl font-bold text-black font-extrabold">Policy Details</h1>
                <p class="text-slate-600 mt-2">View and manage complete information for this policy</p>
            </div>
            <div class="flex items-center gap-4">
                <button 
                    onclick="printPolicy()" 
                    class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2 shadow-sm"
                >
                    <i class="fas fa-print"></i>
                    Print Policy
                </button>
                <a href="{{ route('admin.policies.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-600 hover:text-black hover:border-black transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Sidebar (No Print) -->
            <div class="lg:col-span-1 space-y-6 no-print">
                <!-- Image Card -->
                @if ($policy->image)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="aspect-square bg-slate-100 flex items-center justify-center">
                            <img src="{{ url('files/' . $policy->image) }}" alt="Policy Flowchart/Image" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <a href="{{ url('files/' . $policy->image) }}" target="_blank" class="w-full bg-slate-200 text-slate-800 py-3 rounded-lg hover:bg-slate-350 transition text-center font-semibold flex items-center justify-center gap-2">
                                <i class="fas fa-external-link-alt"></i>
                                View Full Size
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="aspect-square bg-slate-50 flex items-center justify-center border-b border-slate-100">
                            <div class="text-center">
                                <i class="fas fa-file-contract text-4xl text-slate-300 mb-3"></i>
                                <p class="text-slate-500 text-sm">No flowchart attached</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Meta Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="text-xs uppercase font-bold text-slate-600 tracking-wider mb-4 border-b border-slate-100 pb-2">Record Info</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Policy ID</p>
                            <p class="text-indigo-650 font-black text-sm">#{{ $policy->policy_id ?? 'N/A' }}</p>
                        </div>
                        @if($policy->type)
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Policy Type</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $policy->type }}
                            </span>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Database ID</p>
                            <p class="text-slate-900 font-medium">#{{ $policy->id }}</p>
                        </div>
                        @if($policy->category)
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Category</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $policy->category }}
                            </span>
                        </div>
                        @endif
                        @if($policy->department)
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Department</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $policy->department }}
                            </span>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Created</p>
                            <p class="text-slate-900 font-medium">{{ $policy->created_at->format('M d, Y \a\t H:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold mb-1">Last Updated</p>
                            <p class="text-slate-900 font-medium">{{ $policy->updated_at->format('M d, Y \a\t H:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Action Buttons -->
                <div class="flex flex-col gap-3">
                    <a href="{{ route('admin.policies.edit', $policy->id) }}" class="w-full bg-black text-white py-3 rounded-lg hover:bg-slate-900 transition font-semibold flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-edit"></i>
                        Edit Policy Details
                    </a>
                    <button onclick="confirmDelete({{ $policy->id }})" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Policy
                    </button>
                    <form id="delete-form" action="{{ route('admin.policies.destroy', $policy->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Right Content / Policy Document (Will Print) -->
            <div class="lg:col-span-3">
                <div id="policyPrintArea" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 md:p-16">
                    <!-- Policy Header block -->
                    <div class="text-center mb-10 pb-8 border-b-2 border-slate-200">
                        <h2 class="text-slate-500 uppercase tracking-widest font-black text-sm mb-2">IT Investment Recoveries</h2>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4 print-title">
                            @if($policy->category)
                                <span class="inline-block bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1 rounded-xl text-sm font-black uppercase mr-2 align-middle">
                                    {{ $policy->category }}
                                </span>
                            @endif
                            {{ $policy->name }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-slate-600 mt-4">
                            <div>
                                <span class="font-bold text-slate-800">Policy ID:</span> {{ $policy->policy_id ?? 'N/A' }}
                            </div>
                            @if($policy->type)
                                <div>
                                    <span class="font-bold text-slate-800">Type:</span> {{ $policy->type }}
                                </div>
                            @endif
                            @if($policy->category)
                                <div>
                                    <span class="font-bold text-slate-800">Category:</span> {{ $policy->category }}
                                </div>
                            @endif
                            @if($policy->department)
                                <div>
                                    <span class="font-bold text-slate-800">Department:</span> {{ $policy->department }}
                                </div>
                            @endif
                            @if($policy->effective_date)
                                <div>
                                    <span class="font-bold text-slate-800">Effective Date:</span> {{ \Carbon\Carbon::parse($policy->effective_date)->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Policy Content -->
                    <div class="space-y-8 text-slate-800 leading-relaxed print-body">
                        <!-- Dynamic Policy Sections -->
                        @if($policy->sections && count($policy->sections) > 0)
                            @foreach($policy->sections as $sec)
                                @if(!empty($sec['title']) && !empty($sec['content']))
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">{{ $sec['title'] }}</h3>
                                        @if(($sec['type'] ?? 'paragraph') === 'bullet')
                                            <ul class="list-disc pl-6 space-y-2 text-slate-800">
                                                @foreach(explode("\n", str_replace("\r", "", $sec['content'])) as $line)
                                                    @if(trim($line) !== '')
                                                        <li>{{ ltrim(trim($line), '•-* ') }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="whitespace-pre-line">{{ $sec['content'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <!-- Fallback to Legacy Fields if sections JSON is empty -->
                            <!-- 1. Purpose -->
                            @if($policy->purpose)
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">1. Purpose</h3>
                                    <p class="whitespace-pre-line">{{ $policy->purpose }}</p>
                                </div>
                            @endif

                            <!-- 2. Scope -->
                            @if($policy->scope)
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">2. Scope</h3>
                                    <p class="whitespace-pre-line">{{ $policy->scope }}</p>
                                </div>
                            @endif

                            @if(in_array($policy->type, ['Company Policy & Procedure', 'Refurbishing Policy']))
                                <!-- Refurbishing / Policy & Procedure Specific Sections -->
                                @if($policy->safety)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">3. Safety Guidelines</h3>
                                        <p class="whitespace-pre-line">{{ $policy->safety }}</p>
                                    </div>
                                @endif

                                @if($policy->visual_inspection)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">4. Visual Inspection</h3>
                                        <p class="whitespace-pre-line">{{ $policy->visual_inspection }}</p>
                                    </div>
                                @endif

                                @if($policy->functional_testing)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">5. Functional Testing</h3>
                                        <p class="whitespace-pre-line">{{ $policy->functional_testing }}</p>
                                    </div>
                                @endif

                                @if($policy->final_cleaning)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">6. Final Cleaning</h3>
                                        <p class="whitespace-pre-line">{{ $policy->final_cleaning }}</p>
                                    </div>
                                @endif

                                @if($policy->cosmetic_grading)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">7. Cosmetic Grading</h3>
                                        <p class="whitespace-pre-line">{{ $policy->cosmetic_grading }}</p>
                                    </div>
                                @endif

                                @if($policy->inventory_update)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">8. Inventory Update</h3>
                                        <p class="whitespace-pre-line">{{ $policy->inventory_update }}</p>
                                    </div>
                                @endif
                            @else
                                <!-- Standard Policy Sections -->
                                <!-- 3. Warehouse Objectives -->
                                @if($policy->objectives)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">3. Warehouse Objectives</h3>
                                        <p class="whitespace-pre-line">{{ $policy->objectives }}</p>
                                    </div>
                                @endif

                                <!-- 4. Employee Responsibilities -->
                                @if($policy->responsibilities)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">4. Employee Responsibilities</h3>
                                        <p class="whitespace-pre-line">{{ $policy->responsibilities }}</p>
                                    </div>
                                @endif

                                <!-- 5. Receiving Procedures -->
                                @if($policy->receiving_procedures)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">5. Receiving Procedures</h3>
                                        <p class="whitespace-pre-line">{{ $policy->receiving_procedures }}</p>
                                    </div>
                                @endif

                                <!-- 6. Staging Procedures -->
                                @if($policy->staging_procedures)
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">6. Staging Procedures</h3>
                                        <p class="whitespace-pre-line">{{ $policy->staging_procedures }}</p>
                                    </div>
                                @endif
                            @endif

                            <!-- 7. Policy Violations -->
                            @if(!in_array($policy->type, ['Company Procedure', 'Procedures']) && $policy->violations)
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">Policy Violations</h3>
                                    <p class="whitespace-pre-line">{{ $policy->violations }}</p>
                                </div>
                            @endif

                            <!-- Legacy/Reason for Policy -->
                            @if(!$policy->purpose && $policy->reason)
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">Reason</h3>
                                    <p class="whitespace-pre-line">{{ $policy->reason }}</p>
                                </div>
                            @endif

                            <!-- Legacy/Remarks for Policy -->
                            @if($policy->remarks)
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3 print-section-header">Remarks / Notes</h3>
                                    <p class="whitespace-pre-line">{{ $policy->remarks }}</p>
                                </div>
                            @endif
                        @endif

                        <!-- Employee Acknowledgment and Signature block -->
                        <div class="mt-12 pt-8 border-t border-slate-200">
                            @if($policy->employee_acknowledgment)
                                <div class="mb-8 bg-slate-50 rounded-xl p-6 border border-slate-100 print-no-border print-no-bg">
                                    <h4 class="font-bold text-slate-900 mb-2 uppercase text-xs tracking-wider">Employee Acknowledgment</h4>
                                    <p class="text-sm italic text-slate-700">{{ $policy->employee_acknowledgment }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8 print-grid">
                                <!-- Employee Signature Box -->
                                <div class="border border-slate-200 rounded-xl p-6 print-no-border print-no-bg">
                                    <h4 class="font-bold text-slate-900 uppercase text-xs tracking-wider mb-4 border-b border-slate-100 pb-2">Employee</h4>
                                    <div class="space-y-4 text-sm mt-4">
                                        <div class="flex items-end gap-2 h-8">
                                            <span class="text-slate-500 font-semibold w-24">Name:</span>
                                            <div class="flex-1 border-b border-slate-300 h-6"></div>
                                        </div>
                                        <div class="flex items-end gap-2 h-8">
                                            <span class="text-slate-500 font-semibold w-24">Signature:</span>
                                            <div class="flex-1 border-b border-slate-300 h-6"></div>
                                        </div>
                                        <div class="flex items-end gap-2 h-8">
                                            <span class="text-slate-500 font-semibold w-24">Date:</span>
                                            <div class="flex-1 border-b border-slate-300 h-6"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Manager Signature Box -->
                                <div class="border border-slate-200 rounded-xl p-6 print-no-border print-no-bg">
                                    <h4 class="font-bold text-slate-900 uppercase text-xs tracking-wider mb-4 border-b border-slate-100 pb-2">Authorization Manager</h4>
                                    <div class="space-y-4 text-sm mt-4">
                                        <div class="flex items-end gap-2 h-8">
                                            <span class="text-slate-500 font-semibold w-24">Name:</span>
                                            <div class="flex-1 border-b border-slate-300 font-bold text-slate-900 h-6">
                                                {{ $policy->manager_name ?? 'Sal Daniel' }}
                                            </div>
                                        </div>
                                        <div class="flex items-end gap-2 h-8">
                                            <span class="text-slate-500 font-semibold w-24">Signature:</span>
                                            <div class="flex-1 border-b border-slate-300 font-serif italic font-bold text-slate-900 h-6">
                                                {{ $policy->manager_signature ?? 'Sal Daniel' }}
                                            </div>
                                        </div>
                                        <div class="flex items-end gap-2 h-8">
                                            <span class="text-slate-500 font-semibold w-24">Date:</span>
                                            <div class="flex-1 border-b border-slate-300 text-slate-900 h-6">
                                                {{ $policy->effective_date ? \Carbon\Carbon::parse($policy->effective_date)->format('M d, Y') : '_________________' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this policy record? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }

    function printPolicy() {
        window.print();
    }
</script>
@endsection
