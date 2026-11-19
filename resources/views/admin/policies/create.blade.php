@extends('layouts.admin')

@section('title', 'Create Policy Record')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-100 to-blue-50/30 p-8">
    <div class="max-w-8xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-8">
            <a href="{{ route('admin.policies.index') }}" class="text-slate-700 hover:text-black transition">Policy Records</a>
            <span class="text-slate-400 mx-3">/</span>
            <span class="text-slate-900 font-semibold">Create New</span>
        </nav>

        <!-- Header and Template Action -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900">Create New Policy Record</h1>
                <p class="text-slate-600 mt-2">Add a structured company policy with sections, procedures, and manager sign-off</p>
            </div>
            <div>
                <button 
                    type="button" 
                    onclick="loadEWasteTemplate()" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all font-bold"
                >
                    <i class="fas fa-file-import"></i>
                    Load E-Waste Policy Template
                </button>
            </div>
        </div>

        <form action="{{ route('admin.policies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl mb-8 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        <span class="font-bold">Please correct the following errors:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Cards Grid -->
            <div class="space-y-8">
                <!-- Section 1: Header Metadata -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-150 p-8">
                    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                            <i class="fas fa-heading text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">General Information</h2>
                            <p class="text-sm text-slate-500">Provide the title, classification, and effective date</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Policy ID -->
                        <div>
                            <label for="policy_id" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Policy ID
                            </label>
                            <input 
                                type="text" 
                                id="policy_id" 
                                name="policy_id" 
                                value="{{ old('policy_id', $nextPolicyId) }}"
                                readonly
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 cursor-not-allowed font-bold text-indigo-600 focus:outline-none"
                            >
                            @error('policy_id')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Policy Type -->
                        <div>
                            <label for="type" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Policy Type <span class="text-red-600">*</span>
                            </label>
                            <select 
                                id="type" 
                                name="type" 
                                required
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                            >
                                <option value="">Select Type</option>
                                <option value="Company Policy" {{ old('type') == 'Company Policy' ? 'selected' : '' }}>Company Policy</option>
                                <option value="Company Procedure" {{ old('type') == 'Company Procedure' ? 'selected' : '' }}>Company Procedure</option>
                                <option value="Company Policy & Procedure" {{ old('type') == 'Company Policy & Procedure' ? 'selected' : '' }}>Company Policy & Procedure</option>
                            </select>
                            @error('type')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department Dropdown -->
                        <div>
                            <label for="department" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Department
                            </label>
                            <select 
                                id="department" 
                                name="department" 
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                            >
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->department }}" {{ old('department') == $dept->department ? 'selected' : '' }}>
                                        {{ $dept->department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Effective Date -->
                        <div>
                            <label for="effective_date" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Effective Date
                            </label>
                            <input 
                                type="date" 
                                id="effective_date" 
                                name="effective_date" 
                                value="{{ old('effective_date') }}"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                            >
                            @error('effective_date')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Policy Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Policy Name / Title <span class="text-red-600">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}"
                                placeholder="e.g. E-Waste Recycling Receiving & Staging Policy"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                                required
                            >
                            @error('name')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Policy Category -->
                        <div>
                            <label for="category" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Policy Category
                            </label>
                            <input 
                                type="text" 
                                id="category" 
                                name="category" 
                                value="{{ old('category') }}"
                                placeholder="e.g. Receiving & Staging Policy"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                            >
                            @error('category')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Dynamic Policy Sections -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-150 p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                <i class="fas fa-file-alt text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Policy Content Sections</h2>
                                <p class="text-sm text-slate-500">Define the heading, format, and content for each section</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                type="button" 
                                onclick="loadDefaultSections()" 
                                class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl transition font-semibold text-sm border border-slate-200"
                            >
                                <i class="fas fa-undo text-xs"></i>
                                Load Defaults for Selected Type
                            </button>
                            <button 
                                type="button" 
                                onclick="addSection()" 
                                class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-xl transition font-semibold text-sm border border-indigo-100"
                            >
                                <i class="fas fa-plus text-xs"></i>
                                Add Custom Section
                            </button>
                        </div>
                    </div>

                    <div id="dynamic-sections-container">
                        <!-- Dynamic sections will be inserted here -->
                    </div>

                    <div class="flex justify-center mt-6">
                        <button 
                            type="button" 
                            onclick="addSection()" 
                            class="inline-flex items-center gap-2 bg-slate-900 hover:bg-black text-white px-6 py-3 rounded-xl transition font-bold shadow-sm"
                        >
                            <i class="fas fa-plus"></i>
                            Add Custom Section
                        </button>
                    </div>
                </div>

                <!-- Section 4: Compliance & Authorization -->
                <div id="compliance-section" class="bg-white rounded-2xl shadow-sm border border-slate-150 p-8">
                    <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                            <i class="fas fa-signature text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Compliance & Authorization</h2>
                            <p class="text-sm text-slate-500">Document rules, violations, acknowledgments, and sign-offs</p>
                        </div>
                    </div>

                    <div class="space-y-6">

                        <!-- Employee Acknowledgment -->
                        <div id="ack-container">
                            <label for="employee_acknowledgment" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                Employee Acknowledgment Text
                            </label>
                            <textarea 
                                id="employee_acknowledgment" 
                                name="employee_acknowledgment" 
                                rows="3"
                                placeholder="Text that the employee acknowledges and signs"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                            >{{ old('employee_acknowledgment') }}</textarea>
                            @error('employee_acknowledgment')
                                <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Manager Name -->
                            <div>
                                <label for="manager_name" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                    Manager Name
                                </label>
                                <input 
                                    type="text" 
                                    id="manager_name" 
                                    name="manager_name" 
                                    value="{{ old('manager_name') }}"
                                    placeholder="e.g. Sal Daniel"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                                >
                                @error('manager_name')
                                    <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Manager Signature -->
                            <div>
                                <label for="manager_signature" class="block text-sm uppercase font-bold text-slate-600 mb-2">
                                    Manager Signature
                                </label>
                                <input 
                                    type="text" 
                                    id="manager_signature" 
                                    name="manager_signature" 
                                    value="{{ old('manager_signature') }}"
                                    placeholder="e.g. Sal Daniel"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition"
                                >
                                @error('manager_signature')
                                    <p class="text-red-600 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6">
                    <button 
                        type="submit" 
                        class="flex-1 bg-black hover:bg-slate-900 text-white py-4 rounded-xl transition font-bold flex items-center justify-center gap-2 shadow-sm"
                    >
                        <i class="fas fa-save text-lg"></i>
                        Create Policy
                    </button>
                    <a 
                        href="{{ route('admin.policies.index') }}" 
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-900 py-4 rounded-xl transition font-bold flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-times text-lg"></i>
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let sectionIndex = 0;

    const defaultSections = {
        'Company Policy': [
            { title: 'Purpose', type: 'paragraph', content: '' },
            { title: 'Scope', type: 'paragraph', content: '' },
            { title: 'Warehouse Objectives / Core Goals', type: 'bullet', content: '' },
            { title: 'Employee Responsibilities', type: 'bullet', content: '' }
        ],
        'Company Procedure': [
            { title: 'Purpose', type: 'paragraph', content: '' },
            { title: 'Scope', type: 'paragraph', content: '' },
            { title: 'Employee Responsibilities', type: 'bullet', content: '' },
            { title: 'Receiving Procedures', type: 'paragraph', content: '' },
            { title: 'Staging Procedures & Designated Areas', type: 'paragraph', content: '' }
        ],
        'Company Policy & Procedure': [
            { title: 'Purpose', type: 'paragraph', content: '' },
            { title: 'Scope', type: 'paragraph', content: '' },
            { title: 'Safety Guidelines', type: 'paragraph', content: '' },
            { title: 'Visual Inspection & Functional Testing', type: 'paragraph', content: '' },
            { title: 'Receiving & Staging Procedures', type: 'paragraph', content: '' },
            { title: 'Final Cleaning & Inventory Update', type: 'paragraph', content: '' }
        ]
    };

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function addSection(title = '', type = 'paragraph', content = '') {
        const container = document.getElementById('dynamic-sections-container');
        const html = `
            <div class="section-row bg-slate-50/50 border border-slate-200/80 rounded-2xl p-6 relative group transition hover:border-slate-300 mb-6">
                <button type="button" onclick="this.closest('.section-row').remove(); updateSectionNumbers();" class="absolute top-6 right-6 text-slate-400 hover:text-red-600 transition duration-150" title="Remove Section">
                    <i class="fas fa-trash-alt text-lg"></i>
                </button>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xs uppercase font-extrabold px-2.5 py-1 bg-slate-200 text-slate-700 rounded-md section-badge">Section 1</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm uppercase font-bold text-slate-600 mb-2">Section Heading / Title <span class="text-red-500">*</span></label>
                        <input type="text" name="sections[${sectionIndex}][title]" value="${escapeHtml(title)}" required class="w-full px-4 py-3 border border-slate-350 rounded-xl focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition" placeholder="e.g. Purpose">
                    </div>
                    <div>
                        <label class="block text-sm uppercase font-bold text-slate-600 mb-2">Section Type</label>
                        <select name="sections[${sectionIndex}][type]" class="w-full px-4 py-3 border border-slate-350 rounded-xl focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition">
                            <option value="paragraph" ${type === 'paragraph' ? 'selected' : ''}>Paragraph Text</option>
                            <option value="bullet" ${type === 'bullet' ? 'selected' : ''}>Bullet Points</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm uppercase font-bold text-slate-600 mb-2">Section Content <span class="text-red-500">*</span></label>
                        <textarea name="sections[${sectionIndex}][content]" rows="4" required class="w-full px-4 py-3 border border-slate-350 rounded-xl focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition" placeholder="Enter details or list items (one per line for bullets)...">${escapeHtml(content)}</textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        sectionIndex++;
        updateSectionNumbers();
    }

    function updateSectionNumbers() {
        const badges = document.querySelectorAll('.section-badge');
        badges.forEach((badge, idx) => {
            badge.textContent = `Section ${idx + 1}`;
        });
    }

    function loadDefaultSections(force = false) {
        const selectedType = typeSelect.value;
        if (!selectedType) return;
        
        const container = document.getElementById('dynamic-sections-container');
        if (!force && container.children.length > 0) {
            if (!confirm("Loading defaults will clear your current sections. Do you want to proceed?")) {
                return;
            }
        }
        
        container.innerHTML = '';
        sectionIndex = 0;
        
        const sections = defaultSections[selectedType] || [];
        sections.forEach(sec => {
            addSection(sec.title, sec.type, sec.content);
        });
    }

    // Dynamic Policy Template Loader
    function loadEWasteTemplate() {
        document.getElementById('name').value = "E-Waste Recycling Receiving & Staging Policy";
        document.getElementById('category').value = "Receiving & Staging Policy";
        document.getElementById('department').value = "Warehouse Operations";
        
        // Set effective date to today in local timezone
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('effective_date').value = today;

        // Clear existing dynamic sections
        const container = document.getElementById('dynamic-sections-container');
        container.innerHTML = '';
        sectionIndex = 0;

        addSection('1. Purpose', 'paragraph', `The purpose of this policy is to establish standardized procedures for receiving, inspecting, documenting, staging, storing, and securing electronic waste (e-waste) at IT Investment Recoveries. Proper receiving and staging practices help ensure employee safety, environmental compliance, protection of customer assets, data security, accurate inventory management, and efficient processing.`);
        
        addSection('2. Scope', 'paragraph', `This policy applies to all employees, temporary workers, contractors, and authorized visitors involved in receiving, unloading, staging, storing, transporting, or processing electronic equipment at any IT Investment Recoveries warehouse or customer location.`);
        
        addSection('3. Warehouse Objectives / Core Goals', 'bullet', `Protect customer property.
Maintain documented chain of custody.
Prevent unauthorized access to customer assets.
Reduce workplace hazards.
Improve warehouse organization.
Support equipment reuse whenever practical.
Maximize responsible recycling and material recovery.
Maintain compliance with applicable environmental, safety, and data security requirements.`);
        
        addSection('4. Employee Responsibilities', 'bullet', `Wear required personal protective equipment (PPE).
Follow safe lifting procedures.
Verify incoming shipments.
Document received materials.
Keep customer assets separated.
Maintain clean staging areas.
Report damaged or hazardous materials immediately.
Follow all company safety and security procedures.`);
        
        addSection('5. Receiving Procedures', 'paragraph', `5.1 Verify Shipment
Employees shall:
• Verify the customer's name.
• Review the packing list, bill of lading, or pickup documentation (if provided).
• Confirm shipment details.
• Assign a receiving number in the EMS database.
• Record the arrival date and time.

5.2 Inspect Incoming Materials
Inspect for:
• Shipping damage
• Water damage
• Fire damage
• Broken glass
• Damaged batteries
• Leaking batteries
• Chemical spills
• Missing pallets
• Missing equipment
• Damaged containers
Any hazardous condition shall be reported immediately.

5.3 Photograph Shipment
Photographs should include:
• Entire shipment
• Individual pallets on weight scale with weights
• Damaged equipment
• Asset labels (when applicable)
• Shipment condition before unloading
Photos shall be retained with customer records.

5.4 EMS Data Entry
Record:
• Customer name
• Receiving number
• Number of pallets
• Number of boxes
• Estimated equipment count
• Estimated shipment weight
• Employee receiving shipment
• Special handling instructions
• Visible damage
• Put-away location
• Print Bar Code Pallet Label
• Complete Receiving`);
        
        addSection('6. Staging Procedures & Designated Areas', 'paragraph', `After receiving, equipment shall be moved to designated staging areas.
Each staging area shall be clearly identified and used only for its intended purpose.
Recommended staging areas include:

Area A – Incoming Customer Equipment
• Newly received equipment
• Awaiting inventory
• Awaiting inspection
• Customer assets remain grouped together

Area B – Data-Bearing Devices
Includes hard drives, SSDs, servers, laptops, desktop computers, mobile devices, and storage arrays.
Requirements: Secure access, limited employee access, chain of custody maintained, locked storage when unattended.

Area C – Battery Storage
Store batteries separately. Separate by chemistry when practical: Lithium-ion, Nickel-metal hydride, Lead-acid, Alkaline. Damaged or defective batteries shall be isolated in approved containers.

Area D – CRT and Glass Storage
CRT monitors and televisions should be stored separately to minimize breakage and facilitate proper downstream recycling.`);
        
        addSection('7. Policy Violations', 'paragraph', `Failure to follow this policy may result in corrective action, including:
• Coaching
• Retraining
• Written warning
• Suspension
• Termination of employment
depending on the nature and severity of the violation.`);

        document.getElementById('employee_acknowledgment').value = `I acknowledge that I have read and understand the IT Investment Recoveries E-Waste Recycling Receiving & Staging Warehouse Policy. I agree to follow these procedures and understand that compliance is a condition of my employment.`;
        
        document.getElementById('manager_name').value = "Sal Daniel";
        document.getElementById('manager_signature').value = "Sal Daniel";
        
        // Visual notification
        alert("E-Waste Receiving & Staging Policy template loaded successfully! Please verify the values before saving.");
    }

    // Dynamic Fields Toggle based on Policy Type
    const typeSelect = document.getElementById('type');
    const complianceSection = document.getElementById('compliance-section');

    function togglePolicyTypeFields() {
        // Compliance & Authorization Visibility: Always visible at the end of the form
        if (complianceSection) complianceSection.classList.remove('hidden');
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            togglePolicyTypeFields();
            
            // Auto-load defaults on type change only if container is currently empty
            const container = document.getElementById('dynamic-sections-container');
            if (container.children.length === 0) {
                loadDefaultSections(true);
            }
        });
        togglePolicyTypeFields(); // Initial call
    }

    // Handle old inputs on validation redirect
    @if(old('sections'))
        document.addEventListener("DOMContentLoaded", function() {
            const oldSections = {!! json_encode(old('sections')) !!};
            oldSections.forEach(sec => {
                addSection(sec.title || '', sec.type || 'paragraph', sec.content || '');
            });
        });
    @else
        document.addEventListener("DOMContentLoaded", function() {
            loadDefaultSections(true);
        });
    @endif
</script>
@endsection
