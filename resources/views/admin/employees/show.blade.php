@extends('layouts.admin')

@push('styles')
<style>
    @media print {
        nav, header, aside, .no-print, a, button, .flex.items-center.justify-end {
            display: none !important;
        }
        body, main {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .lg\:ml-68 {
            margin-left: 0 !important;
        }
        .max-w-7xl {
            max-width: 100% !important;
            width: 100% !important;
        }
        .grid {
            display: block !important;
        }
        .lg\:col-span-8, .lg\:col-span-4, .lg\:col-span-12 {
            width: 100% !important;
            display: block !important;
            margin-bottom: 20px !important;
        }
        .shadow-sm, .shadow-xl {
            box-shadow: none !important;
        }
        .border {
            border: 1px solid #e2e8f0 !important;
        }
        .rounded-\[2rem\], .rounded-3xl {
            border-radius: 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#F8FAFC] p-4 lg:p-10 antialiased text-slate-900 font-sans">
    
    <div class="max-w-7xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.employees.index') }}" class="h-10 w-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl shadow-sm text-slate-400 hover:text-black transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-950">Personnel File</h1>
                <p class="text-xs font-bold text-slate-450 uppercase tracking-widest mt-0.5">Reference ID: {{ $employee->employee_id }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <div class="hidden md:block text-right mr-4">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Account Status</p>
                <span class="text-xs font-black uppercase tracking-tight {{ $employee->is_resigned ? 'text-rose-600 bg-rose-50 border border-rose-150 px-2 py-0.5 rounded' : 'text-emerald-600 bg-emerald-50 border border-emerald-150 px-2 py-0.5 rounded' }}">
                    {{ $employee->is_resigned ? 'Offboarded' : 'Active Member' }}
                </span>
            </div>
            <a href="{{ route('admin.employees.edit', $employee->id) }}" 
               class="px-5 py-2.5 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-indigo-750 transition-all flex items-center shadow-md">
                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Record
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Sidebar Dossier -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 relative overflow-hidden">
                <div class="flex flex-col items-center">
                    <div class="relative mb-6">
                        @if($employee->employee_picture)
                            @php
                                $pictureUrl = (strpos($employee->employee_picture, '/') !== false || strpos($employee->employee_picture, 'employees/') === 0)
                                    ? asset('storage/' . $employee->employee_picture)
                                    : asset('employee-profile/' . $employee->employee_picture);
                            @endphp
                            <img src="{{ $pictureUrl }}" 
                                 class="w-36 h-36 rounded-2xl object-cover border border-slate-200 shadow-sm">
                        @else
                            @php
                                $bgColors = ['bg-indigo-600', 'bg-purple-600', 'bg-blue-600', 'bg-teal-600', 'bg-emerald-600', 'bg-pink-600', 'bg-amber-600'];
                                $bgIndex = crc32($employee->name) % count($bgColors);
                                $avatarBg = $bgColors[$bgIndex];
                            @endphp
                            <div class="w-36 h-36 rounded-2xl {{ $avatarBg }} flex items-center justify-center text-white text-5xl font-bold shadow-sm">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute -bottom-2 -right-2 bg-white p-1.5 rounded-xl shadow-sm border border-slate-100">
                            <div class="w-3 h-3 rounded-full {{ $employee->is_resigned ? 'bg-slate-350' : 'bg-emerald-500 animate-pulse' }}"></div>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-slate-900 text-center">{{ $employee->name }}</h2>
                    <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">
                        {{ data_get($employee->application_data, 'selected_policy_title', $employee->designation_id ?? 'Unassigned Role') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Body Particulars -->
        <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Personnel Particulars</h3>
                    <span class="text-[9px] font-black px-2.5 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded uppercase">Confidential</span>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-x-10 text-sm">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Father's Name</label>
                            <p class="font-bold text-slate-700 leading-tight">{{ $employee->father_name ?? '---' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">CNIC / ID Number</label>
                            <p class="font-bold text-slate-700">{{ $employee->cnic ?? '---' }}</p>
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Date of Birth</label>
                            <p class="font-bold text-slate-700">{{ $employee->dob ?? '---' }}</p>
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Email Address</label>
                            <p class="font-bold text-slate-700 break-all">{{ $employee->email }}</p>
                        </div>

                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Mobile Contact</label>
                            <p class="font-bold text-slate-700">{{ $employee->mobile_number ?? '---' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between px-2 text-[9px] font-black text-slate-400 uppercase tracking-widest no-print">
                <span>Created At: {{ $employee->created_at->format('d M Y') }}</span>
                <span>Last Updated: {{ $employee->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    <!-- Application Dossier Card -->
    @if($employee->application_data)
    @php
        $appData = $employee->application_data;
    @endphp
    <div id="applicationDossierCard" class="max-w-7xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-8">
        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between no-print bg-slate-50/50">
            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Employment Application & Policy</h3>
            <button onclick="window.print()" class="text-xs bg-indigo-650 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition font-bold shadow-sm">
                <i class="fa-solid fa-print mr-1"></i> Print Application
            </button>
        </div>

        <div class="p-8 space-y-8">
            <!-- Policy Details and Signature -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/60">
                <h4 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-200 pb-2 uppercase tracking-wide">Policy Acknowledgment</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm mb-6">
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Policy Title</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['selected_policy_title'] ?? '---' }}</p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Department</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['selected_policy_department'] ?? '---' }}</p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Effective Date</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['policy_effective_date'] ?? '---' }}</p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Signed At</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['signed_at'] ?? '---' }}</p>
                    </div>
                </div>

                @if($policy)
                    <div class="mt-6 pt-6 border-t border-slate-200 space-y-4 text-xs text-slate-700 leading-relaxed max-h-96 overflow-y-auto bg-white p-5 rounded-xl border border-slate-200/60 shadow-inner">
                        <h5 class="font-bold text-slate-950 border-b pb-2 uppercase tracking-wider text-[10px]">Signed Policy Document Terms</h5>
                        
                        @if($policy->sections && count($policy->sections) > 0)
                            @foreach($policy->sections as $sec)
                                @if(!empty($sec['title']) && !empty($sec['content']))
                                    <div>
                                        <h6 class="font-bold text-slate-900 mb-1">{{ $sec['title'] }}</h6>
                                        @if(($sec['type'] ?? 'paragraph') === 'bullet')
                                            <ul class="list-disc pl-6 space-y-2 text-[11px] text-slate-650">
                                                @foreach(explode("\n", str_replace("\r", "", $sec['content'])) as $line)
                                                    @if(trim($line) !== '')
                                                        <li>{{ ltrim(trim($line), '•-* ') }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $sec['content'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <!-- Fallback to Legacy Fields if sections JSON is empty -->
                            <!-- 1. Purpose -->
                            @if($policy->purpose)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">1. Purpose</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->purpose }}</p>
                                </div>
                            @endif

                            <!-- 2. Scope -->
                            @if($policy->scope)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">2. Scope</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->scope }}</p>
                                </div>
                            @endif

                            <!-- 3. Warehouse Objectives -->
                            @if($policy->objectives)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">3. Warehouse Objectives</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->objectives }}</p>
                                </div>
                            @endif

                            <!-- 4. Employee Responsibilities -->
                            @if($policy->responsibilities)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">4. Employee Responsibilities</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->responsibilities }}</p>
                                </div>
                            @endif

                            <!-- 5. Receiving Procedures -->
                            @if($policy->receiving_procedures)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">5. Receiving Procedures</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->receiving_procedures }}</p>
                                </div>
                            @endif

                            <!-- 6. Staging Procedures -->
                            @if($policy->staging_procedures)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">6. Staging Procedures</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->staging_procedures }}</p>
                                </div>
                            @endif

                            <!-- 7. Policy Violations -->
                            @if($policy->violations)
                                <div>
                                    <h6 class="font-bold text-slate-900 mb-1">7. Policy Violations</h6>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{{ $policy->violations }}</p>
                                </div>
                            @endif

                            <!-- Legacy fallback -->
                            @if(!$policy->purpose && ($policy->content || $policy->description || $policy->body))
                                <div>
                                    <p class="whitespace-pre-line text-[11px] text-slate-650">{!! nl2br(e($policy->content ?? $policy->description ?? $policy->body)) !!}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6 pt-6 border-t border-slate-200">
                    <!-- Employee Policy Signature -->
                    <div>
                        <span class="block text-[9px] font-black text-slate-400 uppercase mb-2">Employee Signature</span>
                        @if(!empty($appData['policy_signature_path']))
                            <div class="border border-slate-200 bg-white rounded-xl p-4 inline-block shadow-sm">
                                <img src="{{ asset('employee-signature/' . basename($appData['policy_signature_path'])) }}" class="max-h-20 object-contain">
                            </div>
                        @else
                            <p class="text-xs font-bold text-rose-600 italic">Not Signed</p>
                        @endif
                    </div>

                    <!-- Manager Signature -->
                    <div>
                        <span class="block text-[9px] font-black text-slate-400 uppercase">Manager Countersign</span>
                        <div class="mt-2 text-xs">
                            <p class="font-bold text-slate-700">Name: {{ $appData['policy_manager_name'] ?? 'Sal Daniel' }}</p>
                            <p class="font-serif italic font-bold text-slate-600 mt-1">Sig: {{ $appData['policy_manager_signature'] ?? 'Sal Daniel' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Details -->
            <div class="space-y-8">
                <h4 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-2 uppercase tracking-wide">Employment Application Details</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div class="md:col-span-3">
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Primary Address</span>
                        <p class="font-bold text-slate-700 mt-0.5">
                            {{ $appData['address'] ?? '' }}, {{ $appData['city'] ?? '' }}, {{ $appData['state'] ?? '' }} {{ $appData['zip'] ?? '' }}
                        </p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Legally Authorized to Work?</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['authorized_to_work'] ?? 'No' }}</p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">At Least 18 Years Old?</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['age_18_or_above'] ?? 'No' }}</p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Worked for Us Before?</span>
                        <p class="font-bold text-slate-700 mt-0.5">
                            {{ $appData['worked_before'] ?? 'No' }} 
                            @if(!empty($appData['worked_before_when'])) (When: {{ $appData['worked_before_when'] }}) @endif
                        </p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Valid Driver's License?</span>
                        <p class="font-bold text-slate-700 mt-0.5">
                            {{ $appData['valid_driver_license'] ?? 'No' }}
                            @if(!empty($appData['driver_license_number'])) (No: {{ $appData['driver_license_number'] }} - {{ $appData['state_issued'] ?? '' }}) @endif
                        </p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Reliable Transportation?</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['reliable_transportation'] ?? 'No' }}</p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Overtime Willingness?</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $appData['overtime'] ?? 'No' }}</p>
                    </div>
                </div>

                <!-- Position details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6 text-sm">
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Position Desired</span>
                        <p class="font-bold text-slate-700 mt-0.5">
                            @if(!empty($appData['position_desired']))
                                {{ is_array($appData['position_desired']) ? implode(', ', $appData['position_desired']) : $appData['position_desired'] }}
                            @else
                                ---
                            @endif
                            @if(!empty($appData['position_other'])) (Other: {{ $appData['position_other'] }}) @endif
                        </p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Desired Pay Rate</span>
                        <p class="font-bold text-slate-700 mt-0.5">
                            {{ $appData['desired_pay_rate'] ?? '---' }}
                        </p>
                    </div>
                </div>

                <!-- Availability -->
                <div class="border-t border-slate-100 pt-6 text-sm">
                    <span class="block text-[9px] font-black text-slate-455 uppercase mb-1">Availability Schedule</span>
                    <p class="font-bold text-slate-750">
                        @if(!empty($appData['availability']))
                            {{ is_array($appData['availability']) ? implode(', ', $appData['availability']) : $appData['availability'] }}
                        @else
                            Not Specified
                        @endif
                    </p>
                </div>

                <!-- Education -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6 text-sm">
                    <div>
                        <span class="block text-[9px] font-black text-slate-400 uppercase mb-2">High School</span>
                        <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-205 space-y-1">
                            <p class="font-bold text-slate-750">Name: {{ data_get($appData, 'high_school.school_name') ?: '---' }}</p>
                            <p class="text-xs text-slate-500">Location: {{ data_get($appData, 'high_school.city_state') ?: '---' }}</p>
                            <p class="text-xs text-slate-500">Graduated: {{ data_get($appData, 'high_school.graduated') ?: '---' }} (Year: {{ data_get($appData, 'high_school.year') ?: '---' }})</p>
                        </div>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-400 uppercase mb-2">College / Trade School</span>
                        <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-205 space-y-1">
                            <p class="font-bold text-slate-750">Name: {{ data_get($appData, 'college.school_name') ?: '---' }}</p>
                            <p class="text-xs text-slate-500">Degree: {{ data_get($appData, 'college.degree') ?: '---' }}</p>
                            <p class="text-xs text-slate-500">Graduated: {{ data_get($appData, 'college.graduated') ?: '---' }} (Year: {{ data_get($appData, 'college.year') ?: '---' }})</p>
                        </div>
                    </div>
                </div>

                <!-- Experience -->
                @if(!empty($appData['employers']))
                    <div class="border-t border-slate-100 pt-6 text-sm space-y-4">
                        <span class="block text-[9px] font-black text-slate-400 uppercase">Work Experience</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($appData['employers'] as $idx => $employer)
                                @if(!empty($employer['company_name']))
                                    <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-200/60 space-y-2">
                                        <div class="flex justify-between items-start">
                                            <p class="font-bold text-slate-700">{{ $employer['company_name'] }}</p>
                                            <span class="text-[9px] font-bold bg-slate-200 px-2 py-0.5 rounded text-slate-650">Supervisor: {{ $employer['supervisor'] ?? '---' }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 text-slate-550">Role: {{ $employer['position'] ?? '---' }} ({{ $employer['from'] ?? '' }} - {{ $employer['to'] ?? '' }})</p>
                                        <p class="text-xs text-slate-500 text-slate-550 italic">Responsibilities: {{ $employer['responsibilities'] ?? '---' }}</p>
                                        <p class="text-xs text-slate-550 italic">Reason for Leaving: {{ $employer['reason_for_leaving'] ?? '---' }}</p>
                                        <p class="text-xs text-slate-600 font-bold">Contact Allowed: {{ $employer['may_contact'] ?? 'No' }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Skills and Certifications -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6 text-sm">
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Technical Skills</span>
                        <p class="font-bold text-slate-700 mt-1">
                            @if(!empty($appData['skills']))
                                {{ is_array($appData['skills']) ? implode(', ', $appData['skills']) : $appData['skills'] }}
                            @else
                                Not specified
                            @endif
                            @if(!empty($appData['skills_other'])) (Other: {{ $appData['skills_other'] }}) @endif
                        </p>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-slate-450 uppercase">Certifications & Licenses</span>
                        <p class="font-bold text-slate-700 mt-1 whitespace-pre-line">{{ $appData['certifications'] ?? 'None listed' }}</p>
                    </div>
                </div>

                <!-- References -->
                @if(!empty($appData['references']))
                    <div class="border-t border-slate-100 pt-6 text-sm space-y-4">
                        <span class="block text-[9px] font-black text-slate-400 uppercase">Professional References</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($appData['references'] as $ref)
                                @if(!empty($ref['name']))
                                    <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-205 space-y-1">
                                        <p class="font-bold text-slate-750">{{ $ref['name'] }} ({{ $ref['relationship'] ?? 'Reference' }})</p>
                                        <p class="text-xs text-slate-500">Phone: {{ $ref['phone'] ?? '---' }} | Email: {{ $ref['email'] ?? '---' }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Emergency Contact -->
                @if(!empty($appData['emergency_contact']))
                    <div class="border-t border-slate-100 pt-6 text-sm">
                        <span class="block text-[9px] font-black text-slate-400 uppercase mb-2">Emergency Contact</span>
                        <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 inline-block space-y-1">
                            <p class="font-bold text-indigo-950">{{ data_get($appData, 'emergency_contact.name') ?: '---' }} ({{ data_get($appData, 'emergency_contact.relationship') ?: '---' }})</p>
                            <p class="text-xs text-indigo-850 font-bold">Phone: {{ data_get($appData, 'emergency_contact.phone') ?: '---' }}</p>
                        </div>
                    </div>
                @endif

                <!-- Applicant Statement & Signature -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6 text-sm">
                    <div>
                        <span class="block text-[9px] font-black text-slate-400 uppercase mb-2">Applicant Signature Acknowledgment</span>
                        @if(!empty($appData['applicant_signature_path']))
                            <div class="border border-slate-200 bg-white rounded-xl p-4 inline-block shadow-sm">
                                <img src="{{ asset('employee-signature/' . basename($appData['applicant_signature_path'])) }}" class="max-h-20 object-contain">
                            </div>
                            <p class="text-xs text-slate-400 mt-2">Signed on: {{ $appData['applicant_signature_date'] ?? '---' }}</p>
                        @else
                            <p class="text-xs font-bold text-rose-600 italic">Not Signed</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection