@extends('layouts.admin')

@section('content')
    <style>
        .application-paper {
            font-family: Georgia, "Times New Roman", serif;
            color: #111;
        }

        .application-paper input,
        .application-paper textarea,
        .application-paper select {
            font-family: Georgia, "Times New Roman", serif;
        }

        .paper-input {
            border: 0;
            border-bottom: 2px solid #222;
            border-radius: 0;
            background: transparent;
            outline: none;
            padding: 2px 6px;
            height: 32px;
            width: 100%;
            font-size: 18px;
        }

        .paper-textarea {
            border: 0;
            border-bottom: 2px solid #222;
            border-radius: 0;
            background: transparent;
            outline: none;
            padding: 4px 6px;
            width: 100%;
            min-height: 46px;
            font-size: 18px;
            resize: vertical;
        }

        .paper-input:focus,
        .paper-textarea:focus {
            border-bottom-color: #000;
            box-shadow: none;
        }

        .paper-row {
            display: flex;
            align-items: end;
            gap: 8px;
            margin-bottom: 18px;
        }

        .paper-label {
            font-weight: 700;
            font-size: 19px;
            white-space: nowrap;
        }

        .section-title {
            font-size: 40px;
            line-height: 1.1;
            font-weight: 700;
            margin-top: 42px;
            margin-bottom: 24px;
        }

        .small-title {
            font-size: 24px;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .paper-check {
            appearance: none;
            -webkit-appearance: none;
            width: 17px;
            height: 17px;
            border: 2px solid #111;
            display: inline-block;
            margin-right: 7px;
            vertical-align: -2px;
            background: #fff;
        }

        .paper-check:checked {
            background: #111;
            box-shadow: inset 0 0 0 3px #fff;
        }

        .check-line {
            font-size: 19px;
            margin-bottom: 10px;
        }

        .wizard-step.hidden {
            display: none;
        }

        .step-badge {
            width: 34px;
            height: 34px;
            border: 2px solid #111;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 700;
        }

        .step-badge.active {
            background: #111;
            color: #fff;
        }

        .policy-document {
            border: 2px solid #111;
            padding: 28px;
            margin-top: 20px;
            background: #fff;
        }

        .policy-document.hidden {
            display: none;
        }

        .policy-text {
            font-size: 18px;
            line-height: 1.65;
        }

        .signature-box {
            border: 2px solid #111;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
        }

        .signature-canvas {
            width: 100%;
            height: 180px;
            display: block;
            cursor: crosshair;
            touch-action: none;
            background: #fff;
        }

        @media(max-width: 768px) {
            .paper-row {
                flex-wrap: wrap;
                align-items: start;
            }

            .section-title {
                font-size: 32px;
            }
        }

        @media print {
            .no-print, nav, header, aside, .lg\:ml-68, .step-badge, #prevStepBtn, #nextStepBtn, #submitBtn, #printReviewBtn, .min-h-screen > div:first-child {
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
            .wizard-step {
                display: none !important;
            }
            .wizard-step[data-step="5"] {
                display: block !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .application-paper {
                box-shadow: none !important;
                border: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>

    @php
        $positions = [
            'Warehouse Associate',
            'Electronics Recycling Technician',
            'Computer Technician',
            'Driver',
            'Office Administrator',
            'Sales Representative',
            'General Labor',
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $skills = [
            'Computer Repair',
            'Laptop Repair',
            'Data Wiping',
            'Hard Drive Destruction',
            'Warehouse Experience',
            'Forklift Experience',
            'Pallet Jack',
            'Box Truck Driving',
            'Customer Service',
            'Inventory Management',
            'Microsoft Office',
            'Shipping & Receiving',
            'eBay / Online Sales',
            'Electronics Recycling',
        ];
    @endphp

    <div class="min-h-screen bg-slate-100 py-8 px-4">
        <div class="max-w-7xl mx-auto mb-5 flex justify-between items-center no-print">
            <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-white border rounded-lg text-sm font-semibold">
                ← Back to Directory
            </a>

            <button onclick="window.print()" type="button"
                class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">
                Print Form
            </button>
        </div>

        @if ($errors->any())
            <div class="max-w-7xl mx-auto mb-5 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg no-print">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.employees.store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="employeeCreateForm"
              class="application-paper max-w-7xl mx-auto bg-white shadow-xl px-8 md:px-12 py-10">
            @csrf

            <div class="no-print flex gap-4 mb-8 justify-center">
                <div class="flex items-center gap-2">
                    <span class="step-badge active" data-step-badge="1">1</span>
                    <span class="text-sm font-bold">Details</span>
                </div>
                <div class="h-[2px] w-8 bg-slate-300 self-center"></div>
                <div class="flex items-center gap-2">
                    <span class="step-badge" data-step-badge="2">2</span>
                    <span class="text-sm font-bold">Policy</span>
                </div>
                <div class="h-[2px] w-8 bg-slate-300 self-center"></div>
                <div class="flex items-center gap-2">
                    <span class="step-badge" data-step-badge="3">3</span>
                    <span class="text-sm font-bold">Application</span>
                </div>
                <div class="h-[2px] w-8 bg-slate-300 self-center"></div>
                <div class="flex items-center gap-2">
                    <span class="step-badge" data-step-badge="4">4</span>
                    <span class="text-sm font-bold">Signature</span>
                </div>
                <div class="h-[2px] w-8 bg-slate-300 self-center"></div>
                <div class="flex items-center gap-2">
                    <span class="step-badge" data-step-badge="5">5</span>
                    <span class="text-sm font-bold">Review</span>
                </div>
            </div>

            {{-- STEP 1 --}}
            <div class="wizard-step" data-step="1">
                <h2 class="section-title">Basic Employee Details</h2>

                <div class="paper-row">
                    <label class="paper-label">Employee ID:</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $nextEmployeeId) }}" required readonly class="paper-input bg-slate-100 cursor-not-allowed font-bold text-indigo-600">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Full Name:</label>
                    <input type="text" name="name" id="employee_name" value="{{ old('name') }}" required class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Father Name:</label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Date of Birth:</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" class="paper-input max-w-[280px]">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Email Address:</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Phone Number:</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">CNIC:</label>
                    <input type="text" name="cnic" value="{{ old('cnic') }}" required class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Profile Photo:</label>
                    <input type="file" name="employee_picture" accept="image/*" class="paper-input">
                </div>

                <div class="paper-row mt-5">
                    <label class="paper-label">Administrative Comments:</label>
                    <textarea name="comment" class="paper-textarea">{{ old('comment') }}</textarea>
                </div>

                <div class="check-line mt-4">
                    <label>
                        <input type="checkbox" name="is_resigned" value="1" class="paper-check" @checked(old('is_resigned'))>
                        Resigned?
                    </label>
                </div>
            </div>

            {{-- STEP 2 --}}
            <div class="wizard-step hidden" data-step="2">
                <h2 class="section-title">Select Department Policy</h2>

                <div class="paper-row">
                    <label class="paper-label">Department:</label>
                    <select name="policy_id" id="policy_id" required class="paper-input">
                        <option value="">Select Department Policy</option>
                        @foreach ($policies as $policy)
                            @php
                                $departmentName = $policy->department ?? $policy->department_name ?? $policy->name ?? 'Department';
                                $policyTitle = $policy->title ?? $policy->policy_title ?? $policy->name ?? 'Policy';
                            @endphp
                            <option value="{{ $policy->id }}" @selected(old('policy_id') == $policy->id)>
                                {{ $departmentName }} - {{ $policyTitle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="policyDocuments">
                    @foreach ($policies as $policy)
                        @php
                            $departmentName = $policy->department ?? $policy->department_name ?? $policy->name ?? 'Department';
                            $policyTitle = $policy->title ?? $policy->policy_title ?? $policy->name ?? 'Policy';
                            $policyCategory = $policy->policy_category ?? $policy->category ?? '';
                            $policyBody = $policy->content_html ?? null;
                        @endphp

                        <div class="policy-document hidden" data-policy-id="{{ $policy->id }}">
                            <h2 class="section-title">IT Investment Recoveries</h2>
                            <h3 class="small-title">{{ $policyTitle }}</h3>

                            <p class="policy-text"><strong>Policy Category:</strong> {{ $policyCategory }}</p>
                            <p class="policy-text"><strong>Department:</strong> {{ $departmentName }}</p>

                            <div class="paper-row mt-4">
                                <label class="paper-label">Effective Date:</label>
                                <input type="date"
                                       name="application[policy_effective_date]"
                                       id="policy_effective_date"
                                       class="paper-input max-w-[280px]"
                                       required>
                            </div>

                            <!-- Detailed Policy Fields -->
                             <div class="space-y-6 text-slate-800 leading-relaxed policy-text mt-6">
                                 @if($policy->sections && count($policy->sections) > 0)
                                     @foreach($policy->sections as $sec)
                                         @if(!empty($sec['title']) && !empty($sec['content']))
                                             <div>
                                                 <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">{{ $sec['title'] }}</h4>
                                                 @if(($sec['type'] ?? 'paragraph') === 'bullet')
                                                     <ul class="list-disc pl-6 space-y-2 text-slate-850">
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
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">1. Purpose</h4>
                                             <p class="whitespace-pre-line">{{ $policy->purpose }}</p>
                                         </div>
                                     @endif

                                     <!-- 2. Scope -->
                                     @if($policy->scope)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">2. Scope</h4>
                                             <p class="whitespace-pre-line">{{ $policy->scope }}</p>
                                         </div>
                                     @endif

                                     <!-- 3. Warehouse Objectives -->
                                     @if($policy->objectives)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">3. Warehouse Objectives</h4>
                                             <p class="whitespace-pre-line">{{ $policy->objectives }}</p>
                                         </div>
                                     @endif

                                     <!-- 4. Employee Responsibilities -->
                                     @if($policy->responsibilities)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">4. Employee Responsibilities</h4>
                                             <p class="whitespace-pre-line">{{ $policy->responsibilities }}</p>
                                         </div>
                                     @endif

                                     <!-- 5. Receiving Procedures -->
                                     @if($policy->receiving_procedures)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">5. Receiving Procedures</h4>
                                             <p class="whitespace-pre-line">{{ $policy->receiving_procedures }}</p>
                                         </div>
                                     @endif

                                     <!-- 6. Staging Procedures -->
                                     @if($policy->staging_procedures)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">6. Staging Procedures</h4>
                                             <p class="whitespace-pre-line">{{ $policy->staging_procedures }}</p>
                                         </div>
                                     @endif

                                     <!-- 7. Policy Violations -->
                                     @if($policy->violations)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">7. Policy Violations</h4>
                                             <p class="whitespace-pre-line">{{ $policy->violations }}</p>
                                         </div>
                                     @endif

                                     <!-- Legacy content fallback -->
                                     @if(!$policy->purpose && ($policy->content || $policy->description || $policy->body))
                                         <div>
                                             <p class="whitespace-pre-line">{!! nl2br(e($policy->content ?? $policy->description ?? $policy->body)) !!}</p>
                                         </div>
                                     @endif

                                     <!-- Legacy/Reason for Policy -->
                                     @if(!$policy->purpose && $policy->reason)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">Reason</h4>
                                             <p class="whitespace-pre-line">{{ $policy->reason }}</p>
                                         </div>
                                     @endif

                                     <!-- Legacy/Remarks for Policy -->
                                     @if($policy->remarks)
                                         <div>
                                             <h4 class="font-bold text-slate-900 border-b border-slate-200 pb-1 mb-2">Remarks / Notes</h4>
                                             <p class="whitespace-pre-line">{{ $policy->remarks }}</p>
                                         </div>
                                     @endif
                                 @endif
                             </div>

                            <h3 class="small-title mt-8">Employee Acknowledgment</h3>

                            <p class="policy-text mb-4">
                                @if($policy->employee_acknowledgment)
                                    {{ $policy->employee_acknowledgment }}
                                @else
                                    I acknowledge that I have read and understand this policy.
                                    I agree to follow this policy and all related procedures.
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                <div id="policySignatureArea" class="hidden mt-8">
                    <div class="paper-row">
                        <label class="paper-label">Employee Name:</label>
                        <input type="text"
                               name="application[policy_employee_name]"
                               id="policy_employee_name"
                               readonly
                               required
                               class="paper-input">
                    </div>

                    <label class="paper-label block mb-3">Employee Policy Signature:</label>

                    <div class="signature-box">
                        <canvas id="policySignaturePad" class="signature-canvas"></canvas>
                    </div>

                    <input type="hidden" name="application[policy_signature]" id="policy_signature" required>
                    <input type="hidden" name="application[policy_signed_at]" id="policy_signed_at">

                    <div class="no-print flex gap-3 mt-3">
                        <button type="button"
                                id="clearPolicySignature"
                                class="px-4 py-2 bg-white border border-gray-400 rounded text-sm font-bold">
                            Clear Signature
                        </button>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5 mt-6">
                        <div class="paper-row">
                            <label class="paper-label">Manager Name:</label>
                            <input type="text"
                                   name="application[policy_manager_name]"
                                   value="Sal Daniel"
                                   required
                                   class="paper-input">
                        </div>

                        <div class="paper-row">
                            <label class="paper-label">Manager Signature:</label>
                            <input type="text"
                                   name="application[policy_manager_signature]"
                                   value="Sal Daniel"
                                   required
                                   class="paper-input">
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3 --}}
            <div class="wizard-step hidden" data-step="3">
                <h2 class="section-title">Employment Application</h2>

                <div class="paper-row">
                    <label class="paper-label">Address:</label>
                    <input type="text" name="application[address]" class="paper-input">
                </div>

                <div class="grid md:grid-cols-3 gap-4 mb-5">
                    <div class="paper-row mb-0">
                        <label class="paper-label">City:</label>
                        <input type="text" name="application[city]" class="paper-input">
                    </div>

                    <div class="paper-row mb-0">
                        <label class="paper-label">State:</label>
                        <input type="text" name="application[state]" class="paper-input">
                    </div>

                    <div class="paper-row mb-0">
                        <label class="paper-label">ZIP:</label>
                        <input type="text" name="application[zip]" class="paper-input">
                    </div>
                </div>

                <div class="check-line">
                    Are you legally authorized to work in the United States?
                    <label class="ml-2">
                        <input type="radio" name="application[authorized_to_work]" value="Yes" required class="paper-check">Yes
                    </label>
                    <label class="ml-2">
                        <input type="radio" name="application[authorized_to_work]" value="No" class="paper-check">No
                    </label>
                </div>

                <div class="check-line">
                    Are you at least 18 years of age?
                    <label class="ml-2">
                        <input type="radio" name="application[age_18_or_above]" value="Yes" required class="paper-check">Yes
                    </label>
                    <label class="ml-2">
                        <input type="radio" name="application[age_18_or_above]" value="No" class="paper-check">No
                    </label>
                </div>

                <div class="check-line">
                    Have you ever worked for IT Investment Recoveries before?
                    <label class="ml-2">
                        <input type="radio" name="application[worked_before]" value="Yes" class="paper-check">Yes
                    </label>
                    <label class="ml-2">
                        <input type="radio" name="application[worked_before]" value="No" class="paper-check">No
                    </label>
                </div>

                <div class="paper-row">
                    <label class="paper-label">If yes, when?</label>
                    <input type="text" name="application[worked_before_when]" class="paper-input">
                </div>

                <div class="check-line">
                    Do you have a valid driver's license?
                    <label class="ml-2">
                        <input type="radio" name="application[valid_driver_license]" value="Yes" class="paper-check">Yes
                    </label>
                    <label class="ml-2">
                        <input type="radio" name="application[valid_driver_license]" value="No" class="paper-check">No
                    </label>
                </div>

                <div class="paper-row">
                    <label class="paper-label">Driver's License Number:</label>
                    <input type="text" name="application[driver_license_number]" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">State Issued:</label>
                    <input type="text" name="application[state_issued]" class="paper-input">
                </div>

                <div class="check-line">
                    Do you have reliable transportation?
                    <label class="ml-2">
                        <input type="radio" name="application[reliable_transportation]" value="Yes" class="paper-check">Yes
                    </label>
                    <label class="ml-2">
                        <input type="radio" name="application[reliable_transportation]" value="No" class="paper-check">No
                    </label>
                </div>

                <h2 class="section-title">Position Applying For</h2>

                <p class="paper-label mb-3">Position Desired:</p>

                <div class="grid md:grid-cols-2 gap-x-8 mb-4">
                    @foreach ($positions as $position)
                        <label class="check-line">
                            <input type="checkbox" name="application[position_desired][]" value="{{ $position }}"
                                class="paper-check">
                            {{ $position }}
                        </label>
                    @endforeach

                    <div class="paper-row">
                        <label class="paper-label">Other:</label>
                        <input type="text" name="application[position_other]" class="paper-input">
                    </div>
                </div>

                <div class="paper-row">
                    <label class="paper-label">Desired Start Date:</label>
                    <input type="date" name="joining_date" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Desired Pay Rate:</label>
                    <input type="text" name="application[desired_pay_rate]" class="paper-input">
                </div>

                <p class="paper-label mb-3">Employment Type:</p>
                <div class="grid md:grid-cols-3 mb-5">
                    @foreach (['Full-Time', 'Part-Time', 'Temporary'] as $type)
                        <label class="check-line">
                            <input type="radio" name="application[employment_type]" value="{{ $type }}"
                                class="paper-check">
                            {{ $type }}
                        </label>
                    @endforeach
                </div>

                <h2 class="section-title">Availability</h2>

                <div class="grid md:grid-cols-3 gap-x-8 mb-4">
                    @foreach ($days as $day)
                        <label class="check-line">
                            <input type="checkbox" name="application[availability][]" value="{{ $day }}"
                                class="paper-check">
                            {{ $day }}
                        </label>
                    @endforeach
                </div>

                <div class="check-line">
                    Are you willing to work overtime if needed?
                    <label class="ml-2">
                        <input type="radio" name="application[overtime]" value="Yes" class="paper-check">Yes
                    </label>
                    <label class="ml-2">
                        <input type="radio" name="application[overtime]" value="No" class="paper-check">No
                    </label>
                </div>

                <h2 class="section-title">Education</h2>

                <h3 class="small-title">High School</h3>

                <div class="paper-row">
                    <label class="paper-label">School Name:</label>
                    <input type="text" name="application[high_school][school_name]" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">City/State:</label>
                    <input type="text" name="application[high_school][city_state]" class="paper-input">
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div class="check-line">
                        Graduated?
                        <label class="ml-2">
                            <input type="radio" name="application[high_school][graduated]" value="Yes"
                                class="paper-check">Yes
                        </label>
                        <label class="ml-2">
                            <input type="radio" name="application[high_school][graduated]" value="No"
                                class="paper-check">No
                        </label>
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Year:</label>
                        <input type="text" name="application[high_school][year]" class="paper-input">
                    </div>
                </div>

                <h3 class="small-title">College/Trade School</h3>

                <div class="paper-row">
                    <label class="paper-label">School Name:</label>
                    <input type="text" name="application[college][school_name]" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Degree/Certificate:</label>
                    <input type="text" name="application[college][degree]" class="paper-input">
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div class="check-line">
                        Graduated?
                        <label class="ml-2">
                            <input type="radio" name="application[college][graduated]" value="Yes" class="paper-check">Yes
                        </label>
                        <label class="ml-2">
                            <input type="radio" name="application[college][graduated]" value="No" class="paper-check">No
                        </label>
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Year:</label>
                        <input type="text" name="application[college][year]" class="paper-input">
                    </div>
                </div>

                <h2 class="section-title">Work Experience</h2>

                @for ($i = 1; $i <= 2; $i++)
                    @php $idx = $i - 1; @endphp

                    <h3 class="small-title">Employer #{{ $i }}</h3>

                    <div class="paper-row">
                        <label class="paper-label">Company Name:</label>
                        <input type="text" name="application[employers][{{ $idx }}][company_name]" class="paper-input">
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Position:</label>
                        <input type="text" name="application[employers][{{ $idx }}][position]" class="paper-input">
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Supervisor:</label>
                        <input type="text" name="application[employers][{{ $idx }}][supervisor]" class="paper-input">
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Phone:</label>
                        <input type="text" name="application[employers][{{ $idx }}][phone]" class="paper-input">
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="paper-row">
                            <label class="paper-label">From:</label>
                            <input type="text" name="application[employers][{{ $idx }}][from]" class="paper-input">
                        </div>

                        <div class="paper-row">
                            <label class="paper-label">To:</label>
                            <input type="text" name="application[employers][{{ $idx }}][to]" class="paper-input">
                        </div>
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Job Responsibilities:</label>
                        <textarea name="application[employers][{{ $idx }}][responsibilities]" class="paper-textarea"></textarea>
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Reason for Leaving:</label>
                        <textarea name="application[employers][{{ $idx }}][reason_for_leaving]" class="paper-textarea"></textarea>
                    </div>

                    <div class="check-line">
                        May we contact this employer?
                        <label class="ml-2">
                            <input type="radio" name="application[employers][{{ $idx }}][may_contact]"
                                value="Yes" class="paper-check">Yes
                        </label>
                        <label class="ml-2">
                            <input type="radio" name="application[employers][{{ $idx }}][may_contact]"
                                value="No" class="paper-check">No
                        </label>
                    </div>
                @endfor

                <h2 class="section-title">Skills & Qualifications</h2>

                <p class="paper-label mb-3">Please check all that apply:</p>

                <div class="grid md:grid-cols-2 gap-x-8 mb-4">
                    @foreach ($skills as $skill)
                        <label class="check-line">
                            <input type="checkbox" name="application[skills][]" value="{{ $skill }}"
                                class="paper-check">
                            {{ $skill }}
                        </label>
                    @endforeach
                </div>

                <div class="paper-row">
                    <label class="paper-label">Other:</label>
                    <input type="text" name="application[skills_other]" class="paper-input">
                </div>

                <h2 class="section-title">Certifications</h2>

                <div class="paper-row">
                    <label class="paper-label">List any certifications or licenses:</label>
                    <textarea name="application[certifications]" class="paper-textarea"></textarea>
                </div>

                <h2 class="section-title">References</h2>

                @for ($i = 1; $i <= 2; $i++)
                    @php $idx = $i - 1; @endphp

                    <h3 class="small-title">Reference #{{ $i }}</h3>

                    <div class="paper-row">
                        <label class="paper-label">Name:</label>
                        <input type="text" name="application[references][{{ $idx }}][name]" class="paper-input">
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Relationship:</label>
                        <input type="text" name="application[references][{{ $idx }}][relationship]" class="paper-input">
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Phone:</label>
                        <input type="text" name="application[references][{{ $idx }}][phone]" class="paper-input">
                    </div>

                    <div class="paper-row">
                        <label class="paper-label">Email:</label>
                        <input type="email" name="application[references][{{ $idx }}][email]" class="paper-input">
                    </div>
                @endfor

                <h2 class="section-title">Emergency Contact</h2>

                <div class="paper-row">
                    <label class="paper-label">Name:</label>
                    <input type="text" name="application[emergency_contact][name]" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Relationship:</label>
                    <input type="text" name="application[emergency_contact][relationship]" class="paper-input">
                </div>

                <div class="paper-row">
                    <label class="paper-label">Phone:</label>
                    <input type="text" name="application[emergency_contact][phone]" class="paper-input">
                </div>

                <h2 class="section-title">Applicant Statement</h2>

                <p class="text-[18px] leading-relaxed mb-4">
                    I certify that the information provided in this application is true and complete to the best of my
                    knowledge. I understand that any false statements or omissions may result in the rejection of my
                    application or termination of employment if hired.
                </p>

                <p class="text-[18px] leading-relaxed mb-4">
                    I authorize IT Investment Recoveries to verify the information contained in this application,
                    including employment history, education, and references, to the extent permitted by law.
                </p>

                <p class="text-[18px] leading-relaxed mb-7">
                    If employed, I understand that my employment is "at will," meaning either I or the company may
                    terminate the employment relationship at any time, with or without notice, unless otherwise
                    provided by law.
                </p>
            </div>

            {{-- STEP 4 --}}
            <div class="wizard-step hidden" data-step="4">
                <h2 class="section-title">Applicant Signature</h2>

                <label class="paper-label block mb-3">Applicant Signature:</label>

                <div class="signature-box">
                    <canvas id="applicantSignaturePad" class="signature-canvas"></canvas>
                </div>

                <input type="hidden" name="application[applicant_signature]" id="applicant_signature" required>
                <input type="hidden" name="application[applicant_signed_at]" id="applicant_signed_at">

                <div class="paper-row mt-5">
                    <label class="paper-label">Date:</label>
                    <input type="date"
                           name="application[applicant_signature_date]"
                           id="applicant_signature_date"
                           class="paper-input max-w-[280px]"
                           required>
                </div>

                <div class="no-print flex gap-3 mt-3">
                    <button type="button"
                            id="clearApplicantSignature"
                            class="px-4 py-2 bg-white border border-gray-400 rounded text-sm font-bold">
                        Clear Signature
                    </button>
                </div>
            </div>

            {{-- STEP 5 --}}
            <div class="wizard-step hidden" data-step="5">

                <div class="space-y-8 bg-slate-50 p-6 md:p-8 rounded-2xl border border-slate-200" id="reviewContainer">
                    
                    <!-- Basic details -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">1. Basic Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="block text-xs text-slate-400">Employee ID:</span>
                                <span id="rev_employee_id" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Full Name:</span>
                                <span id="rev_name" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Father's Name:</span>
                                <span id="rev_father_name" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Email Address:</span>
                                <span id="rev_email" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Mobile Contact:</span>
                                <span id="rev_mobile_number" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">CNIC Number:</span>
                                <span id="rev_cnic" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Date of Birth:</span>
                                <span id="rev_dob" class="font-bold text-slate-700"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Policy selection & signature -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">2. Selected Policy & Acknowledgment</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                            <div>
                                <span class="block text-xs text-slate-400">Selected Policy:</span>
                                <span id="rev_policy_name" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Effective Date:</span>
                                <span id="rev_policy_effective_date" class="font-bold text-slate-700"></span>
                            </div>
                        </div>
                        <div class="border border-slate-200 bg-white rounded-xl p-4 inline-block">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Employee Policy Acknowledgment Signature:</span>
                            <img id="rev_policy_signature_img" class="max-h-20 object-contain" src="" alt="Policy Signature">
                        </div>
                    </div>

                    <!-- Employment details -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">3. Employment Application Particulars</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="md:col-span-3">
                                <span class="block text-xs text-slate-400">Current Address:</span>
                                <span id="rev_address" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Authorized to Work?</span>
                                <span id="rev_authorized_to_work" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Age 18 or Older?</span>
                                <span id="rev_age_18_or_above" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Worked for Us Before?</span>
                                <span id="rev_worked_before" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Driver License?</span>
                                <span id="rev_valid_driver_license" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Reliable Transportation?</span>
                                <span id="rev_reliable_transportation" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Willing to Overtime?</span>
                                <span id="rev_overtime" class="font-bold text-slate-700"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t text-sm">
                            <div>
                                <span class="block text-xs text-slate-400">Desired Position(s):</span>
                                <span id="rev_position_desired" class="font-bold text-slate-700"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-slate-400">Desired Pay Rate:</span>
                                <span id="rev_desired_pay_rate" class="font-bold text-slate-700"></span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t text-sm">
                            <span class="block text-xs text-slate-400">Availability:</span>
                            <span id="rev_availability" class="font-bold text-slate-700"></span>
                        </div>
                    </div>

                    <!-- High school / college education -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">4. Education Records</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <span class="block font-bold text-slate-600 mb-1">High School</span>
                                <p class="text-slate-700">School: <span id="rev_hs_name" class="font-bold"></span></p>
                                <p class="text-slate-500">Location: <span id="rev_hs_loc"></span></p>
                                <p class="text-slate-500">Graduated: <span id="rev_hs_grad"></span> (<span id="rev_hs_year"></span>)</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <span class="block font-bold text-slate-600 mb-1">College</span>
                                <p class="text-slate-700">School: <span id="rev_coll_name" class="font-bold"></span></p>
                                <p class="text-slate-500">Degree: <span id="rev_coll_deg"></span></p>
                                <p class="text-slate-500">Graduated: <span id="rev_coll_grad"></span> (<span id="rev_coll_year"></span>)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Work experience -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">5. Work Experience</h3>
                        <div class="space-y-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200 text-sm">
                                <p class="font-bold text-slate-700">Employer 1: <span id="rev_emp1_name"></span></p>
                                <p class="text-slate-600 font-medium mt-1">Position: <span id="rev_emp1_pos"></span> (Supervisor: <span id="rev_emp1_sup"></span>)</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200 text-sm">
                                <p class="font-bold text-slate-700">Employer 2: <span id="rev_emp2_name"></span></p>
                                <p class="text-slate-600 font-medium mt-1">Position: <span id="rev_emp2_pos"></span> (Supervisor: <span id="rev_emp2_sup"></span>)</p>
                            </div>
                        </div>
                    </div>

                    <!-- References & Emergency contact -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">6. References & Emergency Contacts</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <span class="block font-bold text-slate-600 mb-2">Emergency Contact</span>
                                <p class="text-slate-700">Name: <span id="rev_emergency_name" class="font-bold"></span></p>
                                <p class="text-slate-500">Relationship: <span id="rev_emergency_relationship"></span></p>
                                <p class="text-slate-500 font-bold mt-1">Phone: <span id="rev_emergency_phone"></span></p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200">
                                <span class="block font-bold text-slate-600 mb-2">Certifications & Licenses</span>
                                <p id="rev_certifications" class="text-slate-700 font-bold whitespace-pre-wrap"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Signature preview -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 border-b pb-1 mb-3 uppercase tracking-wider text-xs">7. Applicant Signature</h3>
                        <div class="border border-slate-200 bg-white rounded-xl p-4 inline-block">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Applicant Statement Signature:</span>
                            <img id="rev_applicant_signature_img" class="max-h-20 object-contain" src="" alt="Applicant Signature">
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Signed Date: <span id="rev_applicant_signature_date" class="font-bold"></span></p>
                    </div>

                </div>
            </div>

            <div class="no-print flex justify-between gap-3 mt-10 pt-6 border-t">
                <button type="button"
                        id="prevStepBtn"
                        class="px-8 py-3 bg-white border border-gray-300 rounded-lg font-bold hidden">
                    Back
                </button>

                <button type="button"
                        id="nextStepBtn"
                        class="px-8 py-3 bg-black text-white rounded-lg font-bold">
                    Next
                </button>

                <button type="submit"
                        id="submitBtn"
                        class="px-8 py-3 bg-black text-white rounded-lg font-bold hidden">
                    Create Employee
                </button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentStep = 1;
        const totalSteps = 5;

        const steps = document.querySelectorAll('.wizard-step');
        const badges = document.querySelectorAll('[data-step-badge]');
        const nextBtn = document.getElementById('nextStepBtn');
        const prevBtn = document.getElementById('prevStepBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('employeeCreateForm');

        const policySelect = document.getElementById('policy_id');
        const policyDocs = document.querySelectorAll('.policy-document');
        const policySignatureArea = document.getElementById('policySignatureArea');
        const employeeName = document.getElementById('employee_name');
        const policyEmployeeName = document.getElementById('policy_employee_name');

        const today = new Date().toISOString().slice(0, 10);

        document.getElementById('policy_effective_date')?.setAttribute('value', today);
        document.getElementById('applicant_signature_date')?.setAttribute('value', today);

        function populateReviewFields() {
            // 1. Basic details
            document.getElementById('rev_employee_id').textContent = document.getElementsByName('employee_id')[0]?.value || '---';
            document.getElementById('rev_name').textContent = document.getElementById('employee_name')?.value || '---';
            document.getElementById('rev_father_name').textContent = document.getElementsByName('father_name')[0]?.value || '---';
            document.getElementById('rev_email').textContent = document.getElementsByName('email')[0]?.value || '---';
            document.getElementById('rev_mobile_number').textContent = document.getElementsByName('mobile_number')[0]?.value || '---';
            document.getElementById('rev_cnic').textContent = document.getElementsByName('cnic')[0]?.value || '---';
            document.getElementById('rev_dob').textContent = document.getElementsByName('dob')[0]?.value || '---';

            // 2. Policy selection & signature
            const policySelectText = policySelect.options[policySelect.selectedIndex]?.text || '---';
            document.getElementById('rev_policy_name').textContent = policySelectText;
            document.getElementById('rev_policy_effective_date').textContent = document.getElementById('policy_effective_date')?.value || '---';
            document.getElementById('rev_policy_signature_img').src = document.getElementById('policy_signature')?.value || '';

            // 3. Application details
            document.getElementById('rev_address').textContent = (
                (document.getElementsByName('application[address]')[0]?.value || '') + ', ' +
                (document.getElementsByName('application[city]')[0]?.value || '') + ', ' +
                (document.getElementsByName('application[state]')[0]?.value || '') + ' ' +
                (document.getElementsByName('application[zip]')[0]?.value || '')
            ).trim().replace(/^,\s*,\s*$/, '') || '---';

            const authorizedVal = document.querySelector('input[name="application[authorized_to_work]"]:checked')?.value || 'No';
            document.getElementById('rev_authorized_to_work').textContent = authorizedVal;

            const ageVal = document.querySelector('input[name="application[age_18_or_above]"]:checked')?.value || 'No';
            document.getElementById('rev_age_18_or_above').textContent = ageVal;

            const workedBeforeVal = document.querySelector('input[name="application[worked_before]"]:checked')?.value || 'No';
            const workedBeforeWhen = document.getElementsByName('application[worked_before_when]')[0]?.value;
            document.getElementById('rev_worked_before').textContent = workedBeforeVal + (workedBeforeWhen ? ' (' + workedBeforeWhen + ')' : '');

            const licenseVal = document.querySelector('input[name="application[valid_driver_license]"]:checked')?.value || 'No';
            const licenseNo = document.getElementsByName('application[driver_license_number]')[0]?.value;
            const licenseState = document.getElementsByName('application[state_issued]')[0]?.value;
            document.getElementById('rev_valid_driver_license').textContent = licenseVal + (licenseNo ? ' (' + licenseNo + ' - ' + licenseState + ')' : '');

            const transVal = document.querySelector('input[name="application[reliable_transportation]"]:checked')?.value || 'No';
            document.getElementById('rev_reliable_transportation').textContent = transVal;

            const overtimeVal = document.querySelector('input[name="application[overtime]"]:checked')?.value || 'No';
            document.getElementById('rev_overtime').textContent = overtimeVal;

            // Desired Positions
            const checkedPositions = Array.from(document.querySelectorAll('input[name="application[position_desired][]"]:checked')).map(el => el.value);
            const positionOther = document.getElementsByName('application[position_other]')[0]?.value;
            if (positionOther) {
                checkedPositions.push('Other: ' + positionOther);
            }
            document.getElementById('rev_position_desired').textContent = checkedPositions.join(', ') || '---';

            // Pay rate
            document.getElementById('rev_desired_pay_rate').textContent = document.getElementsByName('application[desired_pay_rate]')[0]?.value || '---';

            // Availability
            const checkedDays = Array.from(document.querySelectorAll('input[name="application[availability][]"]:checked')).map(el => el.value);
            document.getElementById('rev_availability').textContent = checkedDays.join(', ') || 'Not Specified';

            // 4. Education
            document.getElementById('rev_hs_name').textContent = document.getElementsByName('application[high_school][school_name]')[0]?.value || '---';
            document.getElementById('rev_hs_loc').textContent = document.getElementsByName('application[high_school][city_state]')[0]?.value || '---';
            document.getElementById('rev_hs_grad').textContent = document.getElementsByName('application[high_school][graduated]')[0]?.value || '---';
            document.getElementById('rev_hs_year').textContent = document.getElementsByName('application[high_school][year]')[0]?.value || '---';

            document.getElementById('rev_coll_name').textContent = document.getElementsByName('application[college][school_name]')[0]?.value || '---';
            document.getElementById('rev_coll_deg').textContent = document.getElementsByName('application[college][degree]')[0]?.value || '---';
            document.getElementById('rev_coll_grad').textContent = document.getElementsByName('application[college][graduated]')[0]?.value || '---';
            document.getElementById('rev_coll_year').textContent = document.getElementsByName('application[college][year]')[0]?.value || '---';

            // 5. Employers
            document.getElementById('rev_emp1_name').textContent = document.getElementsByName('application[employers][0][company_name]')[0]?.value || '---';
            document.getElementById('rev_emp1_pos').textContent = document.getElementsByName('application[employers][0][position]')[0]?.value || '---';
            document.getElementById('rev_emp1_sup').textContent = document.getElementsByName('application[employers][0][supervisor]')[0]?.value || '---';

            document.getElementById('rev_emp2_name').textContent = document.getElementsByName('application[employers][1][company_name]')[0]?.value || '---';
            document.getElementById('rev_emp2_pos').textContent = document.getElementsByName('application[employers][1][position]')[0]?.value || '---';
            document.getElementById('rev_emp2_sup').textContent = document.getElementsByName('application[employers][1][supervisor]')[0]?.value || '---';

            // 6. Emergency Contact & Certifications
            document.getElementById('rev_emergency_name').textContent = document.getElementsByName('application[emergency_contact][name]')[0]?.value || '---';
            document.getElementById('rev_emergency_relationship').textContent = document.getElementsByName('application[emergency_contact][relationship]')[0]?.value || '---';
            document.getElementById('rev_emergency_phone').textContent = document.getElementsByName('application[emergency_contact][phone]')[0]?.value || '---';

            document.getElementById('rev_certifications').textContent = document.getElementsByName('application[certifications]')[0]?.value || 'None listed';

            // 7. Applicant Signature
            document.getElementById('rev_applicant_signature_img').src = document.getElementById('applicant_signature')?.value || '';
            document.getElementById('rev_applicant_signature_date').textContent = document.getElementById('applicant_signature_date')?.value || '---';
        }

        function showStep(stepNumber) {
            steps.forEach(step => {
                step.classList.toggle('hidden', parseInt(step.dataset.step) !== stepNumber);
            });

            badges.forEach(badge => {
                badge.classList.toggle('active', parseInt(badge.dataset.stepBadge) === stepNumber);
            });

            prevBtn.classList.toggle('hidden', stepNumber === 1);
            nextBtn.classList.toggle('hidden', stepNumber === totalSteps);
            submitBtn.classList.toggle('hidden', stepNumber !== totalSteps);

            if (stepNumber === 5) {
                populateReviewFields();
            }

            setTimeout(() => {
                if (stepNumber === 2) {
                    policySignaturePad.resize();
                }

                if (stepNumber === 4) {
                    applicantSignaturePad.resize();
                }
            }, 150);
        }

        function validateCurrentStep() {
            const currentStepElement = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
            const requiredFields = currentStepElement.querySelectorAll('[required]');

            for (const field of requiredFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }

            if (currentStep === 2) {
                if (!policySelect.value) {
                    alert('Please select department policy.');
                    return false;
                }

                if (!document.getElementById('policy_signature').value) {
                    alert('Please sign the selected department policy.');
                    return false;
                }
            }

            if (currentStep === 4) {
                if (!document.getElementById('applicant_signature').value) {
                    alert('Please sign the applicant signature section.');
                    return false;
                }
            }

            return true;
        }

        nextBtn.addEventListener('click', function () {
            if (!validateCurrentStep()) return;

            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        prevBtn.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        policySelect?.addEventListener('change', function () {
            const selectedPolicyId = this.value;

            policyDocs.forEach(doc => {
                doc.classList.toggle('hidden', doc.dataset.policyId !== selectedPolicyId);
            });

            policySignatureArea.classList.toggle('hidden', !selectedPolicyId);

            policySignaturePad.clear();

            if (selectedPolicyId) {
                setTimeout(() => policySignaturePad.resize(), 150);
            }

            updateEmployeePolicyName();
        });

        function updateEmployeePolicyName() {
            if (policyEmployeeName && employeeName) {
                policyEmployeeName.value = employeeName.value;
            }
        }

        employeeName?.addEventListener('input', updateEmployeePolicyName);

        function createSignaturePad(canvasId, inputId, clearBtnId, signedAtId) {
            const canvas = document.getElementById(canvasId);
            const input = document.getElementById(inputId);
            const clearBtn = document.getElementById(clearBtnId);
            const signedAt = document.getElementById(signedAtId);

            if (!canvas) return { resize: () => {}, clear: () => {} };

            const ctx = canvas.getContext('2d');
            let drawing = false;

            function resize() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);

                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;

                ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#000';

                if (!input.value) {
                    clearCanvas();
                }
            }

            function clearCanvas() {
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
            }

            function clear() {
                clearCanvas();
                input.value = '';
                if (signedAt) signedAt.value = '';
            }

            function position(event) {
                const rect = canvas.getBoundingClientRect();

                if (event.touches && event.touches.length > 0) {
                    return {
                        x: event.touches[0].clientX - rect.left,
                        y: event.touches[0].clientY - rect.top
                    };
                }

                return {
                    x: event.clientX - rect.left,
                    y: event.clientY - rect.top
                };
            }

            function start(event) {
                event.preventDefault();
                drawing = true;

                const pos = position(event);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            function draw(event) {
                if (!drawing) return;

                event.preventDefault();

                const pos = position(event);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();

                input.value = canvas.toDataURL('image/png');
                if (signedAt) signedAt.value = new Date().toISOString();
            }

            function stop(event) {
                if (!drawing) return;

                event.preventDefault();
                drawing = false;

                input.value = canvas.toDataURL('image/png');
                if (signedAt) signedAt.value = new Date().toISOString();
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

        const policySignaturePad = createSignaturePad(
            'policySignaturePad',
            'policy_signature',
            'clearPolicySignature',
            'policy_signed_at'
        );

        const applicantSignaturePad = createSignaturePad(
            'applicantSignaturePad',
            'applicant_signature',
            'clearApplicantSignature',
            'applicant_signed_at'
        );

        form?.addEventListener('submit', function (event) {
            if (!document.getElementById('policy_signature').value) {
                event.preventDefault();
                alert('Policy signature is required.');
                currentStep = 2;
                showStep(currentStep);
                return;
            }

            if (!document.getElementById('applicant_signature').value) {
                event.preventDefault();
                alert('Applicant signature is required.');
                currentStep = 4;
                showStep(currentStep);
                return;
            }
        });

        showStep(currentStep);
    });
    </script>
@endsection
