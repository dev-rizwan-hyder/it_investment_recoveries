@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-[#f1f5f9] pt-8 pb-16 px-6">

        {{-- Header Section --}}
        <div class="max-w-8xl mx-auto mb-8 flex justify-between items-end">
            <div>
                <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-widest mb-2">
                    <span class="w-8 h-[2px] bg-emerald-600"></span>
                    Analytics Dashboard
                </div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight">
                    Generate Your <span class="text-emerald-600">ESG Impact</span> Report
                </h1>
            </div>
            <div class="text-right">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Current Session</p>
                <p class="text-slate-700 font-black">{{ date('M d, Y') }}</p>
            </div>
        </div>

        {{-- Main Filter Card --}}
        <div class="max-w-8xl mx-auto bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">

                    {{-- Step 1: Client --}}
                    <div class="space-y-2">
                        <label
                            class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                            <i class="fas fa-user-tie text-emerald-500"></i> Corporate Partner
                        </label>
                        <select id="client_filter"
                            class="w-full bg-slate-50 border-none rounded-xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                            <option value="">Search Client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" data-company="{{ $client->company }}"
                                    data-email="{{ $client->email }}" data-address="{{ $client->address }}"
                                    data-phone="{{ $client->phone }}">
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Step 2: Date Range --}}
                    <div
                        class="grid grid-cols-2 gap-4 bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100/50">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-emerald-700 uppercase tracking-widest ml-1">Date
                                From</label>
                            <input type="date" id="start_date"
                                class="w-full bg-white border-none rounded-lg p-2.5 text-xs font-bold text-slate-700 outline-none shadow-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-emerald-700 uppercase tracking-widest ml-1">Date
                                To</label>
                            <input type="date" id="end_date"
                                class="w-full bg-white border-none rounded-lg p-2.5 text-xs font-bold text-slate-700 outline-none shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Client Info Strip (Hidden by default) --}}
            <div id="client_detail_section" class="hidden border-t border-dashed border-slate-100 bg-slate-50/50 px-8 py-4">
                <div class="flex flex-wrap gap-8">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-emerald-600 shadow-sm border border-slate-100">
                            <i class="fas fa-building text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase leading-none mb-1">Company</p>
                            <p id="info_company" class="text-xs font-black text-slate-700">--</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 border-l border-slate-200 pl-8">
                        <div
                            class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-emerald-600 shadow-sm border border-slate-100">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase leading-none mb-1">Email</p>
                            <p id="info_email" class="text-xs font-black text-slate-700">--</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="reportForm" action="{{ route('admin.report.store') }}" method="POST" class="max-w-8xl mx-auto">
            @csrf
            <input type="hidden" name="intake_type_hidden" id="intake_type_hidden" value="Processing Intake">
            <input type="hidden" name="client_id_hidden" id="client_id_hidden">

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                {{-- Table Toolbar --}}
                <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div class="flex items-center gap-4">
                        <h3 class="text-sm font-black text-slate-800">Available Client Entries</h3>
                        <button type="button" id="btn_check_all"
                            class="text-[10px] font-black text-emerald-600 px-4 py-2 rounded-full bg-emerald-50 hover:bg-emerald-100 transition-all uppercase tracking-widest">
                            Check All
                        </button>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-full">
                        <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">Selection:</span>
                        <span id="count_num" class="text-sm font-black">0</span>
                    </div>
                </div>

                <div class="min-h-[400px] relative">
                    {{-- Empty State --}}
                    <div id="no_data" class="absolute inset-0 flex flex-col items-center justify-center text-center p-20">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                            <i class="fas fa-user-tie text-slate-300 text-2xl"></i>
                        </div>
                        <h4 class="text-slate-800 font-black text-lg">Select a Corporate Partner</h4>
                        <p class="text-slate-400 text-sm max-w-xs mx-auto">Select a client above to display all processing intake entries for that client.</p>
                    </div>

                    <div id="table_container" class="hidden">
                        <table class="w-full">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="p-6 w-16 text-center"></th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-left tracking-widest">
                                        Name / Description</th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-left tracking-widest">
                                        Brand / Barcode</th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-left tracking-widest">
                                        Model / Location</th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-left tracking-widest">
                                        Serial # / Ref</th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-center tracking-widest">
                                        Condition / Status</th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-center tracking-widest">
                                        Quantity</th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-center tracking-widest">
                                        Weight (LBS)</th>
                                </tr>
                            </thead>
                            <tbody id="items_list_body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-center">
                    <button type="submit" id="submitBtn"
                        class="group bg-emerald-600 hover:bg-emerald-700 text-white px-12 py-5 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-emerald-200 flex items-center gap-4">
                        <span id="btnText">Compile ESG Report</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        <span id="btnLoader" class="hidden"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        const clientFilter = document.getElementById('client_filter');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        const detailSection = document.getElementById('client_detail_section');
        const itemsBody = document.getElementById('items_list_body');
        const tableContainer = document.getElementById('table_container');
        const noDataPlaceholder = document.getElementById('no_data');
        const checkAllBtn = document.getElementById('btn_check_all');
        const countNum = document.getElementById('count_num');

        let isAllChecked = false;

        function updateProfile(option) {
            if (!option || !option.value) {
                detailSection.classList.add('hidden');
                return;
            }
            document.getElementById('info_company').innerText = option.dataset.company || 'Private Entity';
            document.getElementById('info_email').innerText = option.dataset.email || 'N/A';

            detailSection.classList.remove('hidden');
        }

        async function fetchItems() {
            const clientId = clientFilter.value;
            const type = 'Processing Intake';
            const sDate = startDateInput.value;
            const eDate = endDateInput.value;

            document.getElementById('intake_type_hidden').value = type;
            document.getElementById('client_id_hidden').value = clientId;

            const selectedOption = clientFilter.options[clientFilter.selectedIndex];
            updateProfile(selectedOption);

            if (!clientId) {
                tableContainer.classList.add('hidden');
                noDataPlaceholder.classList.remove('hidden');
                return;
            }

            noDataPlaceholder.classList.add('hidden');
            tableContainer.classList.remove('hidden');
            itemsBody.innerHTML =
                '<tr><td colspan="8" class="p-20 text-center"><i class="fas fa-circle-notch fa-spin text-emerald-500 text-3xl"></i><p class="mt-2 text-xs font-bold text-slate-400">Fetching Client Entries...</p></td></tr>';

            try {
                const url =
                    `{{ route('admin.report.fetchItems') }}?intake_type=${encodeURIComponent(type)}&client_id=${clientId}&start_date=${sDate}&end_date=${eDate}`;

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const items = await response.json();
                itemsBody.innerHTML = '';

                if (Array.isArray(items) && items.length > 0) {
                    items.forEach(item => {
                        const itemKey = item.unique_key || (item.table_name ? (item.table_name + '_' + item.id) : item.id);
                        const conditionVal = item.condition || 'N/A';
                        const badgeClass =
                            conditionVal === 'Completed' || conditionVal === 'Working' ? 'bg-emerald-100 text-emerald-700' :
                            conditionVal === 'Processing' || conditionVal === 'For Parts' ? 'bg-orange-100 text-orange-700' :
                            conditionVal === 'Received' || conditionVal === 'Refurbished' ? 'bg-blue-100 text-blue-700' :
                            'bg-slate-100 text-slate-700';

                        itemsBody.insertAdjacentHTML('beforeend', `
                    <tr class="hover:bg-slate-50 transition-all group border-b border-slate-50">
                        <td class="p-6 text-center">
                            <input type="checkbox" name="selected_items[]" value="${itemKey}" class="item-checkbox w-5 h-5 rounded border-slate-200 text-emerald-600 focus:ring-emerald-500/20 cursor-pointer">
                        </td>
                        <td class="py-4 px-4 text-sm font-bold text-slate-700 max-w-xs truncate" title="${escapeHtml(item.name || 'N/A')}">${escapeHtml(item.name || 'N/A')}</td>
                        <td class="py-4 px-4 text-sm font-bold text-slate-700">${escapeHtml(item.brand || 'N/A')}</td>
                        <td class="py-4 px-4 text-sm font-bold text-slate-700">${escapeHtml(item.model || 'N/A')}</td>
                        <td class="py-4 px-4 text-sm font-bold text-slate-700">${escapeHtml(item.serial_number || 'N/A')}</td>
                        <td class="py-4 px-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold ${badgeClass}">
                                ${escapeHtml(conditionVal)}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm font-black text-center text-slate-900">${item.quantity || 0}</td>
                        <td class="py-4 px-4 text-sm font-bold text-center text-slate-700">${parseFloat(item.weight || 0).toFixed(1)}</td>
                    </tr>`);
                    });
                } else {
                    itemsBody.innerHTML =
                        '<tr><td colspan="8" class="p-32 text-center text-slate-300 font-bold uppercase text-[10px] tracking-widest">No matching processing entries found for this client</td></tr>';
                }
                updateCounter();
            } catch (e) {
                console.error("Error fetching items:", e);
                itemsBody.innerHTML =
                    '<tr><td colspan="8" class="p-10 text-center text-red-500 font-bold">Failed to load data. Please try again.</td></tr>';
            }
        }

        // 3. Counter Update
        function updateCounter() {
            const checked = document.querySelectorAll('.item-checkbox:checked').length;
            countNum.innerText = checked;
        }

        // 4. Check All Logic
        checkAllBtn.addEventListener('click', () => {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            isAllChecked = !isAllChecked;
            checkboxes.forEach(cb => cb.checked = isAllChecked);
            checkAllBtn.innerText = isAllChecked ? "Uncheck All" : "Check All";
            updateCounter();
        });

        // 5. Event Listeners
        clientFilter.addEventListener('change', fetchItems);
        startDateInput.addEventListener('change', fetchItems);
        endDateInput.addEventListener('change', fetchItems);

        // Dynamic delegation for checkboxes
        itemsBody.addEventListener('change', (e) => {
            if (e.target.classList.contains('item-checkbox')) updateCounter();
        });

        // 6. Form Submission
        document.getElementById('reportForm').addEventListener('submit', (e) => {
            const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one item to generate the report.');
                return;
            }

            // Show Loading on Button
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('btnText').classList.add('hidden');
            document.getElementById('btnLoader').classList.remove('hidden');
        });
    </script>

    <style>
        /* Screenshot-specific styling */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(48%) sepia(13%) saturate(3207%) hue-rotate(114deg) brightness(95%) contrast(80%);
            cursor: pointer;
        }

        .item-checkbox {
            @apply w-5 h-5 rounded border-slate-200 text-emerald-600 focus:ring-emerald-500/20 cursor-pointer;
        }

        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.25em;
            appearance: none;
        }
    </style>
@endsection
