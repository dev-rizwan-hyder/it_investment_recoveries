@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] p-4 lg:p-10 antialiased text-slate-900 font-sans">
    
    <!-- Header Block -->
    <div class="max-w-7xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.employees.index') }}" class="h-10 w-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl shadow-sm text-slate-400 hover:text-black transition-all">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-950">Edit Personnel File</h1>
                <p class="text-xs font-bold text-slate-450 uppercase tracking-widest mt-0.5">Reference ID: {{ $employee->employee_id }}</p>
            </div>
        </div>
        
        <a href="{{ route('admin.employees.index') }}" 
           class="inline-flex items-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-650 hover:text-black transition-colors border border-slate-200 bg-white rounded-xl shadow-sm hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left-long mr-2 text-[10px]"></i> Back to Directory
        </a>
    </div>

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="max-w-7xl mx-auto">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Form Fields Column -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Card 1: Personal Particulars -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Personal Particulars</h3>
                        <i class="fa-solid fa-id-card text-slate-350"></i>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Father Name</label>
                            <input type="text" name="father_name" value="{{ old('father_name', $employee->father_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">CNIC Number</label>
                            <input type="text" name="cnic" value="{{ old('cnic', $employee->cnic) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mobile Number</label>
                            <input type="text" name="mobile_number" value="{{ old('mobile_number', $employee->mobile_number) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob', $employee->dob) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Employment Record -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Employment Record</h3>
                        <i class="fa-solid fa-briefcase text-slate-350"></i>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 text-indigo-650">Employee ID</label>
                            <input type="text" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required readonly class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 cursor-not-allowed font-bold text-indigo-600 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Joining Date</label>
                            <input type="date" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                        </div>
                    </div>
                </div>

                <!-- Card 4: Employment Application (Wizard JSON Data) -->
                @php
                    $appData = $employee->application_data;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.2em]">Application Dossier & Policy</h3>
                        <i class="fa-solid fa-file-signature text-slate-350"></i>
                    </div>

                    <div class="p-8 space-y-8 text-sm">
                        
                        <!-- Policy Selection & Effective Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department Policy</label>
                                <select name="policy_id" id="policy_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium">
                                    <option value="">Select Department Policy</option>
                                    @foreach ($policies as $policy)
                                        @php
                                            $deptName = $policy->department ?? $policy->department_name ?? $policy->name ?? 'Department';
                                            $polTitle = $policy->title ?? $policy->policy_title ?? $policy->name ?? 'Policy';
                                        @endphp
                                        <option value="{{ $policy->id }}" @selected(old('policy_id', data_get($appData, 'selected_policy_id')) == $policy->id)>
                                            {{ $deptName }} - {{ $polTitle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Policy Effective Date</label>
                                <input type="date" name="application[policy_effective_date]" value="{{ old('application.policy_effective_date', data_get($appData, 'policy_effective_date')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-black focus:ring-0 text-sm font-medium">
                            </div>
                        </div>

                        <!-- Dynamic Policy Document Display -->
                        <div id="policyDocuments" class="border border-slate-200 rounded-2xl p-6 bg-slate-50 mt-4">
                            @foreach ($policies as $policy)
                                @php
                                    $deptName = $policy->department ?? $policy->department_name ?? $policy->name ?? 'Department';
                                    $polTitle = $policy->title ?? $policy->policy_title ?? $policy->name ?? 'Policy';
                                    $polCategory = $policy->policy_category ?? $policy->category ?? '';
                                @endphp
                                <div class="policy-document-item hidden space-y-6 text-sm" data-policy-id="{{ $policy->id }}">
                                    <div class="border-b border-slate-200 pb-3">
                                        <h4 class="text-base font-extrabold text-slate-900">{{ $deptName }} - {{ $polTitle }}</h4>
                                        <p class="text-xs text-slate-400 mt-1"><strong>Category:</strong> {{ $polCategory }}</p>
                                    </div>

                                    @if($policy->sections && count($policy->sections) > 0)
                                        @foreach($policy->sections as $sec)
                                            @if(!empty($sec['title']) && !empty($sec['content']))
                                                <div>
                                                    <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">{{ $sec['title'] }}</h5>
                                                    @if(($sec['type'] ?? 'paragraph') === 'bullet')
                                                        <ul class="list-disc pl-6 space-y-2 text-slate-650">
                                                            @foreach(explode("\n", str_replace("\r", "", $sec['content'])) as $line)
                                                                @if(trim($line) !== '')
                                                                    <li>{{ ltrim(trim($line), '•-* ') }}</li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $sec['content'] }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <!-- Fallback to Legacy Fields if sections JSON is empty -->
                                        <!-- 1. Purpose -->
                                        @if($policy->purpose)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">1. Purpose</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->purpose }}</p>
                                            </div>
                                        @endif

                                        <!-- 2. Scope -->
                                        @if($policy->scope)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">2. Scope</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->scope }}</p>
                                            </div>
                                        @endif

                                        <!-- 3. Warehouse Objectives -->
                                        @if($policy->objectives)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">3. Warehouse Objectives</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->objectives }}</p>
                                            </div>
                                        @endif

                                        <!-- 4. Employee Responsibilities -->
                                        @if($policy->responsibilities)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">4. Employee Responsibilities</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->responsibilities }}</p>
                                            </div>
                                        @endif

                                        <!-- 5. Receiving Procedures -->
                                        @if($policy->receiving_procedures)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">5. Receiving Procedures</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->receiving_procedures }}</p>
                                            </div>
                                        @endif

                                        <!-- 6. Staging Procedures -->
                                        @if($policy->staging_procedures)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">6. Staging Procedures</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->staging_procedures }}</p>
                                            </div>
                                        @endif

                                        <!-- 7. Policy Violations -->
                                        @if($policy->violations)
                                            <div>
                                                <h5 class="font-bold text-slate-800 border-b border-slate-200 pb-1 mb-2">7. Policy Violations</h5>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{{ $policy->violations }}</p>
                                            </div>
                                        @endif

                                        <!-- Legacy Fallback -->
                                        @if(!$policy->purpose && ($policy->content || $policy->description || $policy->body))
                                            <div>
                                                <p class="whitespace-pre-line text-xs text-slate-600 leading-relaxed">{!! nl2br(e($policy->content ?? $policy->description ?? $policy->body)) !!}</p>
                                            </div>
                                        @endif
                                    @endif
                                    

                                    <!-- Employee Acknowledgment -->
                                    <div class="mt-6 pt-4 border-t border-slate-200">
                                        <h5 class="font-bold text-slate-850 mb-2">Employee Acknowledgment</h5>
                                        <p class="text-xs text-slate-550 italic">
                                            @if($policy->employee_acknowledgment)
                                                {{ $policy->employee_acknowledgment }}
                                            @else
                                                I acknowledge that I have read and understand this policy. I agree to follow this policy and all related procedures.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Policy Signatures -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Employee Policy Signature</label>
                                
                                @if(!empty($appData['policy_signature_path']))
                                    <div id="existing-policy-signature-container" class="border border-slate-200 bg-white rounded-xl p-4 inline-block relative mb-3">
                                        <img src="{{ asset('employee-signature/' . basename($appData['policy_signature_path'])) }}" class="max-h-20 object-contain">
                                        <button type="button" onclick="clearAndResignPolicy()" class="block mt-2 text-xs text-red-600 font-bold hover:underline">Clear & Re-sign</button>
                                    </div>
                                @endif
                                
                                <div id="policy-signature-pad-container" class="{{ !empty($appData['policy_signature_path']) ? 'hidden' : '' }} space-y-3">
                                    <div class="border border-slate-200 bg-slate-50 rounded-xl overflow-hidden">
                                        <canvas id="policySignaturePad" class="w-full h-36 bg-slate-50 cursor-crosshair touch-none"></canvas>
                                    </div>
                                    <button type="button" id="clearPolicySignature" class="px-3 py-1 bg-white border border-slate-350 text-xs font-bold rounded hover:bg-slate-50">Clear</button>
                                </div>
                                <input type="hidden" name="application[policy_signature]" id="policy_signature" value="{{ !empty($appData['policy_signature_path']) ? asset('employee-signature/' . basename($appData['policy_signature_path'])) : '' }}">
                            </div>

                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 block">Manager Countersign</label>
                                <div class="space-y-2">
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase">Manager Full Name</span>
                                    <input type="text" name="application[policy_manager_name]" value="{{ old('application.policy_manager_name', data_get($appData, 'policy_manager_name', 'Sal Daniel')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                </div>
                                <div class="space-y-2">
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase">Manager Signature Text</span>
                                    <input type="text" name="application[policy_manager_signature]" value="{{ old('application.policy_manager_signature', data_get($appData, 'policy_manager_signature', 'Sal Daniel')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                </div>
                            </div>
                        </div>

                        <!-- Applicant Contact Info -->
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Application Details</h4>
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Street Address</label>
                                    <input type="text" name="application[address]" value="{{ old('application.address', data_get($appData, 'address')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">City</label>
                                        <input type="text" name="application[city]" value="{{ old('application.city', data_get($appData, 'city')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">State</label>
                                        <input type="text" name="application[state]" value="{{ old('application.state', data_get($appData, 'state')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Zip Code</label>
                                        <input type="text" name="application[zip]" value="{{ old('application.zip', data_get($appData, 'zip')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Eligibility Questions -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
                            <div class="space-y-2">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Legally Authorized to Work?</span>
                                <div class="flex space-x-6 py-2">
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[authorized_to_work]" value="Yes" @checked(data_get($appData, 'authorized_to_work') === 'Yes') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> Yes
                                    </label>
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[authorized_to_work]" value="No" @checked(data_get($appData, 'authorized_to_work') === 'No') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> No
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">At Least 18 Years Old?</span>
                                <div class="flex space-x-6 py-2">
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[age_18_or_above]" value="Yes" @checked(data_get($appData, 'age_18_or_above') === 'Yes') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> Yes
                                    </label>
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[age_18_or_above]" value="No" @checked(data_get($appData, 'age_18_or_above') === 'No') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> No
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Worked for Us Before?</span>
                                <div class="flex space-x-6 py-2">
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[worked_before]" value="Yes" @checked(data_get($appData, 'worked_before') === 'Yes') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> Yes
                                    </label>
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[worked_before]" value="No" @checked(data_get($appData, 'worked_before') === 'No') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> No
                                    </label>
                                </div>
                                <input type="text" name="application[worked_before_when]" value="{{ old('application.worked_before_when', data_get($appData, 'worked_before_when')) }}" placeholder="If yes, when?" class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm font-medium">
                            </div>

                            <div class="space-y-2">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Valid Driver's License?</span>
                                <div class="flex space-x-6 py-2">
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[valid_driver_license]" value="Yes" @checked(data_get($appData, 'valid_driver_license') === 'Yes') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> Yes
                                    </label>
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[valid_driver_license]" value="No" @checked(data_get($appData, 'valid_driver_license') === 'No') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> No
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <input type="text" name="application[driver_license_number]" value="{{ old('application.driver_license_number', data_get($appData, 'driver_license_number')) }}" placeholder="License Number" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                    <input type="text" name="application[state_issued]" value="{{ old('application.state_issued', data_get($appData, 'state_issued')) }}" placeholder="State Issued" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Reliable Transportation?</span>
                                <div class="flex space-x-6 py-2">
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[reliable_transportation]" value="Yes" @checked(data_get($appData, 'reliable_transportation') === 'Yes') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> Yes
                                    </label>
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[reliable_transportation]" value="No" @checked(data_get($appData, 'reliable_transportation') === 'No') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> No
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Willing to work Overtime?</span>
                                <div class="flex space-x-6 py-2">
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[overtime]" value="Yes" @checked(data_get($appData, 'overtime') === 'Yes') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> Yes
                                    </label>
                                    <label class="flex items-center text-sm font-medium">
                                        <input type="radio" name="application[overtime]" value="No" @checked(data_get($appData, 'overtime') === 'No') class="w-4 h-4 rounded text-black focus:ring-0 mr-2"> No
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Desired Position & Availability -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">
                            <div class="space-y-3">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Positions Desired</span>
                                <div class="grid grid-cols-2 gap-2">
                                     @php
                                        $positions = ['Warehouse Associate', 'Electronics Recycling Technician', 'Computer Technician', 'Driver', 'Office Administrator', 'Sales Representative', 'General Labor'];
                                        $selectedPositions = data_get($appData, 'position_desired', []);
                                        if(!is_array($selectedPositions)) { $selectedPositions = [$selectedPositions]; }
                                    @endphp
                                    @foreach($positions as $position)
                                        <label class="flex items-center text-xs font-semibold py-1">
                                            <input type="checkbox" name="application[position_desired][]" value="{{ $position }}" @checked(in_array($position, $selectedPositions)) class="w-4 h-4 rounded border-slate-350 text-black focus:ring-0 mr-2">
                                            {{ $position }}
                                        </label>
                                    @endforeach
                                </div>
                                <input type="text" name="application[position_other]" value="{{ old('application.position_other', data_get($appData, 'position_other')) }}" placeholder="Other positions..." class="w-full px-4 py-3 rounded-xl border border-slate-200 mt-2 text-sm font-medium">
                            </div>

                            <div class="space-y-3">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Availability Schedule</span>
                                <div class="grid grid-cols-2 gap-2">
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        $availability = data_get($appData, 'availability', []);
                                        if(!is_array($availability)) { $availability = [$availability]; }
                                    @endphp
                                    @foreach($days as $day)
                                        <label class="flex items-center text-xs font-semibold py-1">
                                            <input type="checkbox" name="application[availability][]" value="{{ $day }}" @checked(in_array($day, $availability)) class="w-4 h-4 rounded border-slate-350 text-black focus:ring-0 mr-2">
                                            {{ $day }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Desired Pay Rate -->
                        <div class="space-y-2 border-t border-slate-100 pt-6">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Desired Pay Rate ($)</label>
                            <input type="text" name="application[desired_pay_rate]" value="{{ old('application.desired_pay_rate', data_get($appData, 'desired_pay_rate')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                        </div>

                        <!-- Education -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">
                            <div class="space-y-4">
                                <span class="block text-[10px] font-black text-slate-900 uppercase tracking-widest">High School</span>
                                <div class="space-y-3 bg-slate-50 p-5 rounded-xl border border-slate-200/60">
                                    <div class="space-y-1">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">School Name</span>
                                        <input type="text" name="application[high_school][school_name]" value="{{ old('application.high_school.school_name', data_get($appData, 'high_school.school_name')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                    </div>
                                    <div class="space-y-1">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">City & State</span>
                                        <input type="text" name="application[high_school][city_state]" value="{{ old('application.high_school.city_state', data_get($appData, 'high_school.city_state')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="space-y-1">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Graduated?</span>
                                            <select name="application[high_school][graduated]" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                                <option value="Yes" @selected(data_get($appData, 'high_school.graduated') === 'Yes')>Yes</option>
                                                <option value="No" @selected(data_get($appData, 'high_school.graduated') === 'No')>No</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Year</span>
                                            <input type="text" name="application[high_school][year]" value="{{ old('application.high_school.year', data_get($appData, 'high_school.year')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <span class="block text-[10px] font-black text-slate-900 uppercase tracking-widest">College / Trade School</span>
                                <div class="space-y-3 bg-slate-50 p-5 rounded-xl border border-slate-200/60">
                                    <div class="space-y-1">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">School Name</span>
                                        <input type="text" name="application[college][school_name]" value="{{ old('application.college.school_name', data_get($appData, 'college.school_name')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                    </div>
                                    <div class="space-y-1">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Degree / Course</span>
                                        <input type="text" name="application[college][degree]" value="{{ old('application.college.degree', data_get($appData, 'college.degree')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="space-y-1">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Graduated?</span>
                                            <select name="application[college][graduated]" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                                <option value="Yes" @selected(data_get($appData, 'college.graduated') === 'Yes')>Yes</option>
                                                <option value="No" @selected(data_get($appData, 'college.graduated') === 'No')>No</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Year</span>
                                            <input type="text" name="application[college][year]" value="{{ old('application.college.year', data_get($appData, 'college.year')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills and Certifications -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">
                            <div class="space-y-3">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Specialized Skills</span>
                                <div class="grid grid-cols-2 gap-2">
                                     @php
                                        $skills = ['Computer Repair', 'Laptop Repair', 'Data Wiping', 'Hard Drive Destruction', 'Warehouse Experience', 'Forklift Experience', 'Pallet Jack', 'Box Truck Driving', 'Customer Service', 'Inventory Management', 'Microsoft Office', 'Shipping & Receiving', 'eBay / Online Sales', 'Electronics Recycling'];
                                        $selectedSkills = data_get($appData, 'skills', []);
                                        if(!is_array($selectedSkills)) { $selectedSkills = [$selectedSkills]; }
                                    @endphp
                                    @foreach($skills as $skill)
                                        <label class="flex items-center text-xs font-semibold py-1">
                                            <input type="checkbox" name="application[skills][]" value="{{ $skill }}" @checked(in_array($skill, $selectedSkills)) class="w-4 h-4 rounded border-slate-350 text-black focus:ring-0 mr-2">
                                            {{ $skill }}
                                        </label>
                                    @endforeach
                                </div>
                                <input type="text" name="application[skills_other]" value="{{ old('application.skills_other', data_get($appData, 'skills_other')) }}" placeholder="Other technical skills..." class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs mt-2">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Certifications & Licenses</label>
                                <textarea name="application[certifications]" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">{{ data_get($appData, 'certifications') }}</textarea>
                            </div>
                        </div>

                        <!-- Card 5: Work Experience -->
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Past Work Experience</h4>
                            <div class="space-y-6">
                                @for($i = 0; $i < 2; $i++)
                                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200/60 space-y-4">
                                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Employer #{{ $i + 1 }}</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Company Name</span>
                                                <input type="text" name="application[employers][{{ $i }}][company_name]" value="{{ old('application.employers.' . $i . '.company_name', data_get($appData, 'employers.' . $i . '.company_name')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                            </div>
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Job Title / Position</span>
                                                <input type="text" name="application[employers][{{ $i }}][position]" value="{{ old('application.employers.' . $i . '.position', data_get($appData, 'employers.' . $i . '.position')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                            </div>
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Supervisor Name</span>
                                                <input type="text" name="application[employers][{{ $i }}][supervisor]" value="{{ old('application.employers.' . $i . '.supervisor', data_get($appData, 'employers.' . $i . '.supervisor')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                            </div>
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Phone Number</span>
                                                <input type="text" name="application[employers][{{ $i }}][phone]" value="{{ old('application.employers.' . $i . '.phone', data_get($appData, 'employers.' . $i . '.phone')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                            </div>
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">May we contact this employer?</span>
                                                <select name="application[employers][{{ $i }}][may_contact]" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                                    <option value="Yes" @selected(data_get($appData, 'employers.' . $i . '.may_contact') === 'Yes')>Yes</option>
                                                    <option value="No" @selected(data_get($appData, 'employers.' . $i . '.may_contact') === 'No')>No</option>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="space-y-1">
                                                    <span class="block text-[9px] font-bold text-slate-400 uppercase">From Date</span>
                                                    <input type="text" name="application[employers][{{ $i }}][from]" value="{{ old('application.employers.' . $i . '.from', data_get($appData, 'employers.' . $i . '.from')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                                </div>
                                                <div class="space-y-1">
                                                    <span class="block text-[9px] font-bold text-slate-400 uppercase">To Date</span>
                                                    <input type="text" name="application[employers][{{ $i }}][to]" value="{{ old('application.employers.' . $i . '.to', data_get($appData, 'employers.' . $i . '.to')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Responsibilities</span>
                                                <textarea name="application[employers][{{ $i }}][responsibilities]" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">{{ data_get($appData, 'employers.' . $i . '.responsibilities') }}</textarea>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Reason for Leaving</span>
                                                <textarea name="application[employers][{{ $i }}][reason_for_leaving]" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">{{ data_get($appData, 'employers.' . $i . '.reason_for_leaving') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Professional References -->
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Professional References</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @for($i = 0; $i < 2; $i++)
                                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200/60 space-y-3">
                                        <h5 class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Reference #{{ $i + 1 }}</h5>
                                        <div class="space-y-1">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Full Name</span>
                                            <input type="text" name="application[references][{{ $i }}][name]" value="{{ old('application.references.' . $i . '.name', data_get($appData, 'references.' . $i . '.name')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                        </div>
                                        <div class="space-y-1">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Relationship</span>
                                            <input type="text" name="application[references][{{ $i }}][relationship]" value="{{ old('application.references.' . $i . '.relationship', data_get($appData, 'references.' . $i . '.relationship')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Phone</span>
                                                <input type="text" name="application[references][{{ $i }}][phone]" value="{{ old('application.references.' . $i . '.phone', data_get($appData, 'references.' . $i . '.phone')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                            </div>
                                            <div class="space-y-1">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Email</span>
                                                <input type="text" name="application[references][{{ $i }}][email]" value="{{ old('application.references.' . $i . '.email', data_get($appData, 'references.' . $i . '.email')) }}" class="w-full px-3 py-2 rounded-lg border border-slate-250 bg-white text-xs">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Emergency Contact</h4>
                            <div class="bg-indigo-50/50 p-5 rounded-xl border border-indigo-150 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <span class="block text-[9px] font-bold text-indigo-700 uppercase">Full Name</span>
                                    <input type="text" name="application[emergency_contact][name]" value="{{ old('application.emergency_contact.name', data_get($appData, 'emergency_contact.name')) }}" class="w-full px-3 py-2 rounded-lg border border-indigo-200 bg-white text-xs">
                                </div>
                                <div class="space-y-1">
                                    <span class="block text-[9px] font-bold text-indigo-700 uppercase">Relationship</span>
                                    <input type="text" name="application[emergency_contact][relationship]" value="{{ old('application.emergency_contact.relationship', data_get($appData, 'emergency_contact.relationship')) }}" class="w-full px-3 py-2 rounded-lg border border-indigo-200 bg-white text-xs">
                                </div>
                                <div class="space-y-1">
                                    <span class="block text-[9px] font-bold text-indigo-700 uppercase">Phone</span>
                                    <input type="text" name="application[emergency_contact][phone]" value="{{ old('application.emergency_contact.phone', data_get($appData, 'emergency_contact.phone')) }}" class="w-full px-3 py-2 rounded-lg border border-indigo-200 bg-white text-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Applicant Signature Pad -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Applicant Signature</label>
                                
                                @if(!empty($appData['applicant_signature_path']))
                                    <div id="existing-applicant-signature-container" class="border border-slate-200 bg-white rounded-xl p-4 inline-block relative mb-3">
                                        <img src="{{ asset('employee-signature/' . basename($appData['applicant_signature_path'])) }}" class="max-h-20 object-contain">
                                        <button type="button" onclick="clearAndResignApplicant()" class="block mt-2 text-xs text-red-600 font-bold hover:underline">Clear & Re-sign</button>
                                    </div>
                                @endif
                                
                                <div id="applicant-signature-pad-container" class="{{ !empty($appData['applicant_signature_path']) ? 'hidden' : '' }} space-y-3">
                                    <div class="border border-slate-200 bg-slate-50 rounded-xl overflow-hidden">
                                        <canvas id="applicantSignaturePad" class="w-full h-36 bg-slate-50 cursor-crosshair touch-none"></canvas>
                                    </div>
                                    <button type="button" id="clearApplicantSignature" class="px-3 py-1 bg-white border border-slate-350 text-xs font-bold rounded hover:bg-slate-50">Clear</button>
                                </div>
                                <input type="hidden" name="application[applicant_signature]" id="applicant_signature" value="{{ !empty($appData['applicant_signature_path']) ? asset('employee-signature/' . basename($appData['applicant_signature_path'])) : '' }}">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 block">Signature Date</label>
                                <input type="date" name="application[applicant_signature_date]" value="{{ old('application.applicant_signature_date', data_get($appData, 'applicant_signature_date')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium">
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Column Settings Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Card: Picture Upload -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em] mb-4">Profile Photo</h3>
                    
                    <div class="relative w-full aspect-square border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center overflow-hidden hover:border-slate-400 transition-colors group">
                        <div id="image-preview" class="absolute inset-0 flex items-center justify-center bg-white">
                            @if($employee->employee_picture)
                                @php
                                    $pictureUrl = (strpos($employee->employee_picture, '/') !== false || strpos($employee->employee_picture, 'employees/') === 0)
                                        ? asset('storage/' . $employee->employee_picture)
                                        : asset('employee-profile/' . $employee->employee_picture);
                                @endphp
                                <img src="{{ $pictureUrl }}" class="w-full h-full object-cover">
                            @else
                                <div id="placeholder-icon" class="text-center text-slate-300 transition-colors group-hover:text-slate-500">
                                    <i class="fa-solid fa-camera text-3xl mb-2 block"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Change Photo</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="employee_picture" id="employee_picture" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewFile()">
                    </div>
                </div>

                <!-- Card: Resigned Status -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-widest group-hover:text-rose-600 transition-colors">Mark as Resigned</span>
                        <input type="checkbox" name="is_resigned" value="1" {{ $employee->is_resigned ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-black focus:ring-0 checked:bg-rose-500 checked:border-rose-500 transition-all">
                    </label>
                </div>

                <!-- Actions: Save & Cancel -->
                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-750 text-white text-xs font-bold uppercase tracking-[0.2em] rounded-xl transition-all shadow-md active:scale-95">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="w-full py-4 bg-white text-slate-400 text-[10px] font-bold text-center uppercase tracking-[0.2em] rounded-xl border border-slate-200 hover:text-slate-650 transition-all shadow-sm">
                        Discard
                    </a>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const totalLeavesInput = document.getElementById('total_leaves');
        const usedLeavesInput = document.getElementById('used_leaves');
        const remainingLeavesInput = document.getElementById('remaining_leaves');

        function calculateRemaining() {
            const total = parseInt(totalLeavesInput.value) || 0;
            const used = parseInt(usedLeavesInput.value) || 0;
            remainingLeavesInput.value = Math.max(0, total - used);
        }

        totalLeavesInput?.addEventListener('input', calculateRemaining);
        usedLeavesInput?.addEventListener('input', calculateRemaining);

        // Preview Profile Photo
        window.previewFile = function() {
            const preview = document.getElementById('image-preview');
            const file = document.getElementById('employee_picture').files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function () {
                preview.innerHTML = `<img src="${reader.result}" class="w-full h-full object-cover">`;
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        };

        // Signature Pad Drawing Logic
        function createSignaturePad(canvasId, inputId, clearBtnId) {
            const canvas = document.getElementById(canvasId);
            const input = document.getElementById(inputId);
            const clearBtn = document.getElementById(clearBtnId);
            if (!canvas) return null;

            const ctx = canvas.getContext('2d');
            let drawing = false;

            function resize() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                ctx.scale(ratio, ratio);
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                clear();
            }

            function clear() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                input.value = '';
            }

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function start(e) {
                e.preventDefault();
                drawing = true;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            function draw(e) {
                if (!drawing) return;
                e.preventDefault();
                const pos = getPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                input.value = canvas.toDataURL('image/png');
            }

            function stop() {
                drawing = false;
            }

            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stop);
            canvas.addEventListener('mouseleave', stop);

            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stop, { passive: false });

            clearBtn?.addEventListener('click', clear);
            window.addEventListener('resize', resize);
            resize();

            return { resize, clear };
        }

        const policySignaturePad = createSignaturePad('policySignaturePad', 'policy_signature', 'clearPolicySignature');
        const applicantSignaturePad = createSignaturePad('applicantSignaturePad', 'applicant_signature', 'clearApplicantSignature');

        window.clearAndResignPolicy = function() {
            document.getElementById('existing-policy-signature-container')?.remove();
            const padContainer = document.getElementById('policy-signature-pad-container');
            if (padContainer) {
                padContainer.classList.remove('hidden');
                policySignaturePad.resize();
            }
            document.getElementById('policy_signature').value = '';
        };

        window.clearAndResignApplicant = function() {
            document.getElementById('existing-applicant-signature-container')?.remove();
            const padContainer = document.getElementById('applicant-signature-pad-container');
            if (padContainer) {
                padContainer.classList.remove('hidden');
                applicantSignaturePad.resize();
            }
            document.getElementById('applicant_signature').value = '';
        };

        // Dynamic Policy Selection Preview
        const policySelect = document.getElementById('policy_id');
        const policyDocs = document.querySelectorAll('.policy-document-item');

        function updatePolicyVisibility() {
            const selectedPolicyId = policySelect.value;
            policyDocs.forEach(doc => {
                doc.classList.toggle('hidden', doc.dataset.policyId !== selectedPolicyId);
            });
        }

        policySelect?.addEventListener('change', updatePolicyVisibility);
        updatePolicyVisibility(); // Run initially to show pre-selected policy
    });
</script>
@endsection