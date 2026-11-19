@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-slate-50 lg:ml-4 transition-all duration-300">
        <div class="max-w-full mx-auto space-y-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Employee Attendance</h1>
                    <p class="text-sm text-slate-500">Track and manage daily presence.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                        onclick="document.getElementById('attendanceReportModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg shadow-sm hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Report
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-white p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" id="checkAll"
                                class="w-5 h-5 rounded border-slate-300 text-[#153d77] focus:ring-[#153d77] transition cursor-pointer">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition">Select
                                All Today</span>
                        </label>

                        <div id="markButtons" class="hidden items-center gap-2 animate-in fade-in slide-in-from-left-2">
                            <div class="h-6 w-px bg-slate-200 mx-2"></div>
                            <button type="button" id="markAllPresent"
                                class="px-4 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-full hover:bg-emerald-600 hover:text-white transition-all">Mark
                                Present</button>
                            <button type="button" id="markAllAbsent"
                                class="px-4 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold rounded-full hover:bg-rose-600 hover:text-white transition-all">Mark
                                Absent</button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.attendance') }}"
                        class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
                        <input type="month" name="month"
                            value="{{ request('month', $selectedMonth ?? now()->format('Y-m')) }}"
                            class="bg-transparent border-none text-sm font-semibold text-slate-700 focus:ring-0 py-1 cursor-pointer">
                        <button type="submit"
                            class="px-4 py-1.5 bg-[#153d77] text-white text-xs font-bold rounded-lg hover:bg-blue-800 transition shadow-sm">Filter</button>
                        <a href="{{ route('admin.attendance') }}"
                            class="px-3 py-1.5 text-slate-500 hover:text-slate-800 text-xs font-bold transition">Reset</a>
                    </form>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-[#153d77] text-white">
                                <th
                                    class="p-4 border-r border-white/10 w-12 text-center text-xs font-black uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="p-4 border-r border-white/10 w-64 text-left sticky left-0 bg-[#153d77] z-30 text-xs font-black uppercase tracking-wider shadow-[2px_0_5px_rgba(0,0,0,0.2)]">
                                    Employee Name</th>
                                @for ($i = 1; $i <= $daysInMonth; $i++)
                                    <th class="p-2 border-r border-white/10 w-12 text-center text-[10px] font-bold">
                                        {{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($employees as $emp)
                                <tr class="group hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center font-bold text-slate-400 bg-slate-50/30">{{ $emp->id }}
                                    </td>
                                    <td
                                        class="p-3 text-left sticky left-0 bg-white group-hover:bg-slate-50 border-r border-slate-100 z-20 shadow-[4px_0_8px_rgba(0,0,0,0.05)]">
                                        <span class="font-bold text-slate-800 leading-tight">{{ $emp->name }}</span>
                                    </td>

                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)
                                                ->startOfMonth()
                                                ->addDays($i - 1)
                                                ->format('Y-m-d');
                                            $attendance = $emp->attendances->firstWhere('attendance_date', $date);
                                            $isToday = $date === now()->format('Y-m-d');
                                        @endphp

                                        <td class="p-2 border-r text-center {{ $isToday ? 'cursor-pointer hover:bg-blue-100 transition-colors' : 'bg-slate-50/20' }}"
                                            @if ($isToday) onclick="openAttendanceModal({{ $emp->id }}, '{{ $emp->name }}', '{{ $date }}')" @endif>

                                            @if ($attendance)
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full font-black text-[10px] 
                                                    {{ $attendance->attendance === 'Present' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                                                    {{ $attendance->attendance === 'Present' ? 'P' : 'A' }}
                                                </span>
                                            @else
                                                <span
                                                    class="text-slate-200 font-bold text-[10px]">{{ $isToday ? '+' : '-' }}</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="attendanceReportModal" class="hidden fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-60 backdrop-blur-sm"
                onclick="document.getElementById('attendanceReportModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="GET" action="{{ route('admin.attendances.report_pdf') }}">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-xl font-bold text-slate-900 mb-6">Generate Attendance Report</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Select
                                    Employee</label>
                                <select name="employee_id"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold focus:ring-2 focus:ring-[#153d77] transition"
                                    required>
                                    <option value="">-- Choose Employee --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Select
                                    Month</label>
                                <input type="month" name="month"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold focus:ring-2 focus:ring-[#153d77] transition"
                                    value="{{ now()->format('Y-m') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-[#153d77] text-white font-bold rounded-xl hover:bg-blue-800 transition shadow-lg">Download
                            PDF</button>
                        <button type="button"
                            onclick="document.getElementById('attendanceReportModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Action Modal --}}
    <div id="actionModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="text-center mb-6">
                <h3 id="modalEmpName" class="text-xl font-black text-slate-800">Name</h3>
                <p id="modalDate" class="text-xs font-bold text-slate-400 uppercase mt-1"></p>
            </div>

            <!-- Attendance History Log - Moved to top for better visibility -->
            <div id="attendanceLog"
                class="mb-6 p-4 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl border-2 border-slate-200 hidden">
                <h4 class="text-[11px] font-black text-slate-600 uppercase mb-4 pb-3 border-b-2 border-slate-300">Today's
                    Timeline</h4>
                <div id="logEntries" class="space-y-3">
                    <!-- Entries populated by JS -->
                </div>
            </div>

            <div class="grid gap-3">
                <div id="checkInArea">
                    <button id="btnCheckIn" onclick="submitAttendance('checkin')"
                        class="w-full py-3 px-4 bg-emerald-500 text-white rounded-2xl font-bold hover:bg-emerald-600 transition flex flex-col items-center justify-center gap-2 shadow-lg cursor-pointer disabled:bg-emerald-100 disabled:cursor-not-allowed disabled:hover:bg-emerald-100 disabled:opacity-75">
                        <span class="text-lg">✓</span>
                        <span class="text-sm">Check In</span>
                    </button>
                </div>

                <!-- Break Button - Shows when checked in -->
                <div id="breakArea" class="hidden">
                    <button id="btnBreak" onclick="submitAttendance('break')"
                        class="w-full py-3 px-4 bg-amber-500 text-white rounded-2xl font-bold hover:bg-amber-600 transition flex flex-col items-center justify-center gap-2 shadow-lg cursor-pointer disabled:bg-amber-200 disabled:cursor-not-allowed disabled:hover:bg-amber-200 disabled:opacity-75">
                        <span class="text-lg">⏸</span>
                        <span class="text-sm">Break</span>
                    </button>
                </div>

                <!-- Resume Button - Shows when on break -->
                <div id="resumeArea" class="hidden">
                    <button id="btnResume" onclick="submitAttendance('resume')"
                        class="w-full py-3 px-4 bg-purple-500 text-white rounded-2xl font-bold hover:bg-purple-600 transition flex flex-col items-center justify-center gap-2 shadow-lg cursor-pointer disabled:bg-purple-200 disabled:cursor-not-allowed disabled:hover:bg-purple-200 disabled:opacity-75">
                        <span class="text-lg">▶</span>
                        <span class="text-sm">Resume Work</span>
                    </button>
                </div>

                <div id="checkOutArea" class="hidden">
                    <button id="btnCheckOut" onclick="submitAttendance('checkout')"
                        class="w-full py-3 px-4 bg-[#153d77] text-white rounded-2xl font-bold hover:bg-blue-800 transition flex flex-col items-center justify-center gap-2 shadow-lg cursor-pointer disabled:bg-blue-100 disabled:cursor-not-allowed disabled:hover:bg-blue-100 disabled:opacity-75">
                        <span class="text-lg">✕</span>
                        <span class="text-sm">Check Out</span>
                    </button>
                </div>

                <div id="absentArea" class="hidden space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">Select Policy</label>
                        <select id="policySelect" 
                            class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-500 focus:border-none transition">
                            <option value="">-- Select Policy --</option>
                            @foreach($policies ?? [] as $policy)
                                <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">Reason for Absence</label>
                        <textarea id="absenceReasonInput" placeholder="Enter reason for absence..."
                            class="w-full p-3 bg-rose-50 border border-rose-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-500 focus:border-none transition resize-none"
                            rows="3"></textarea>
                    </div>
                    <button id="btnAbsent" onclick="submitAttendance('absent')"
                        class="w-full py-3 bg-rose-500 text-white rounded-2xl font-bold hover:bg-rose-600 transition shadow-lg disabled:bg-rose-200 disabled:cursor-not-allowed">
                        Mark Absent
                    </button>
                </div>

                <!-- Show when already marked absent -->
                <div id="absentDisplayArea" class="hidden p-4 bg-rose-50 border-2 border-rose-200 rounded-2xl space-y-2">
                    <p class="text-xs font-black text-rose-600 uppercase mb-2">Status: Absent</p>
                    <p id="absentPolicyDisplay" class="text-sm font-semibold text-rose-700 hidden"><strong>Policy:</strong> <span id="policyName"></span></p>
                    <p id="absentReasonDisplay" class="text-sm font-semibold text-rose-700"></p>
                </div>
                
                <!-- Checkout disabled message -->
                <div id="checkoutDisabledMsg"
                    class="hidden p-4 bg-amber-50 text-amber-700 rounded-2xl font-bold text-center border border-amber-100 text-sm">
                    Checkout closed after 12:00 AM
                </div>
            </div>

            <button onclick="closeModal()"
                class="mt-6 w-full text-slate-400 text-[10px] font-bold uppercase tracking-widest">Close</button>
        </div>
    </div>

    <style>
        .selected-cell {
            @apply ring-2 ring-[#153d77] ring-inset bg-blue-50/50 z-20 !important;
        }

        .disabled-cell {
            @apply pointer-events-none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            @apply bg-slate-100;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            @apply bg-slate-300 rounded-full hover:bg-slate-400;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentEmp, currentDate;

        // 1. Modal Open karne aur Status check karne ka function
        function openAttendanceModal(id, name, date) {
            currentEmp = id;
            currentDate = date;

            $('#modalEmpName').text(name);
            $('#modalDate').text(date);

            // Initial Reset: Show modal and clear display areas
            $('#actionModal').removeClass('hidden');
            $('#checkInArea, #checkOutArea, #breakArea, #resumeArea').removeClass('hidden');
            $('#absentArea, #absentDisplayArea').addClass('hidden');
            
            // Immediately disable all buttons while we load the correct state from server
            // AJAX callback will selectively enable only the correct buttons
            $('#btnCheckIn, #btnCheckOut, #btnAbsent, #btnBreak, #btnResume').prop('disabled', true);

            $('#checkInTimeDisplay, #checkOutTimeDisplay, #breakTimeDisplay, #resumeTimeDisplay, #checkoutDisabledMsg, #breakActiveMsg, #attendanceLog')
                .addClass('hidden');
            $('#checkInTimeDisplay, #checkOutTimeDisplay, #breakTimeDisplay, #resumeTimeDisplay').text('');
            $('#absenceReasonInput').val('');

            $.ajax({
                url: "{{ route('admin.attendance.get_status') }}",
                type: "GET",
                dataType: "json",
                data: {
                    employee_id: id,
                    attendance_date: date
                },
                beforeSend: function() {
                    console.log('Loading attendance status for employee', id, 'on', date);
                },
                success: function(data) {
                    console.log('✓ Attendance status loaded:', data);
                    // Reset submission flag
                    isSubmitting = false;

                    // 1. If Absent, hide action buttons
                    if (data.type === 'Absent') {
                        $('#checkInArea, #checkOutArea, #breakArea, #resumeArea').addClass('hidden');
                        $('#absentDisplayArea').removeClass('hidden');
                        $('#absentArea').addClass('hidden');

                        if (data.policy_name) {
                            $('#policyName').text(data.policy_name);
                            $('#absentPolicyDisplay').removeClass('hidden');
                        } else {
                            $('#absentPolicyDisplay').addClass('hidden');
                        }

                        if (data.absence_reason) {
                            $('#absentReasonDisplay').text('Reason: ' + data.absence_reason);
                        }
                        return;
                    }

                    // 2. Show history log if records exist
                    if (data.records && data.records.length > 0) {
                            displayAttendanceLog(data.records);
                        }

                        const checkoutDisabled = data.checkoutDisabled === true;
                        const records = Array.isArray(data.records) ? data.records : [];

                        console.log('=== MODAL DATA ===');
                        console.log('All records:', records);
                        console.log('data.check_in:', data.check_in);
                        console.log('data.check_out:', data.check_out);

                        const firstCheckin = records.find(r => (r.type || r.action) === 'checkin');
                        const lastCheckout = [...records].reverse().find(r => (r.type || r.action) === 'checkout');
                        const lastBreak = [...records].reverse().find(r => (r.type || r.action) === 'break');
                        const lastResume = [...records].reverse().find(r => (r.type || r.action) === 'resume');

                        console.log('firstCheckin:', firstCheckin);
                        console.log('lastCheckout:', lastCheckout);

                        if (breakTimerInterval) {
                            clearInterval(breakTimerInterval);
                            breakTimerInterval = null;
                        }

                        // Use data.check_in first, fallback to extracted firstCheckin
                        const checkInTime = data.check_in || (firstCheckin ? firstCheckin.time : null);
                        const checkOutTime = data.check_out || (lastCheckout ? lastCheckout.time : null);

                        console.log('checkInTime to display:', checkInTime);
                        console.log('checkOutTime to display:', checkOutTime);

                        if (checkInTime) {
                            console.log('Setting checkInTimeDisplay to:', 'At ' + checkInTime);
                            $('#checkInTimeDisplay').text('At ' + checkInTime).removeClass('hidden');
                            console.log('checkInTimeDisplay element:', $('#checkInTimeDisplay'));
                            console.log('checkInTimeDisplay is visible:', $('#checkInTimeDisplay').is(':visible'));
                        }

                        if (checkOutTime) {
                            console.log('Setting checkOutTimeDisplay to:', 'At ' + checkOutTime);
                            $('#checkOutTimeDisplay').text('At ' + checkOutTime).removeClass('hidden');
                            console.log('checkOutTimeDisplay element:', $('#checkOutTimeDisplay'));
                            console.log('checkOutTimeDisplay is visible:', $('#checkOutTimeDisplay').is(
                                ':visible'));
                        }

                        // 3. Logic based on Last Action
                        if (data.last_action === 'checkin') {

                            $('#btnCheckIn').prop('disabled', true);
                            $('#btnBreak').prop('disabled', false);
                            $('#btnCheckOut').prop('disabled', false);

                            $('#btnResume').prop('disabled', true);
                            $('#btnAbsent').prop('disabled', true);

                            $('#breakArea').removeClass('hidden');
                            $('#resumeArea').addClass('hidden');
                            $('#checkOutArea').removeClass('hidden');
                        } else if (data.last_action === 'break') {

                            $('#btnCheckIn').prop('disabled', true);
                            $('#btnBreak').prop('disabled', true);
                            $('#btnResume').prop('disabled', false);

                            $('#btnCheckOut').prop('disabled', true);
                            $('#btnAbsent').prop('disabled', true);

                            $('#breakArea').addClass('hidden');
                            $('#resumeArea').removeClass('hidden');
                            $('#checkOutArea').addClass('hidden');

                            $('#breakActiveMsg').removeClass('hidden');


                            console.log('On Break');
                            // Get break start time
                            const breakStartTime = lastBreak ? lastBreak.time : '';

                            if (breakStartTime) {
                                $('#resumeTimeDisplay').text('Started at ' + breakStartTime).removeClass('hidden');
                            }

                            // Start break timer
                            startBreakTimer(data.records);
                        } else if (data.last_action === 'resume') {

                            $('#btnCheckIn').prop('disabled', true);
                            $('#btnBreak').prop('disabled', false);
                            $('#btnResume').prop('disabled', true);

                            $('#btnCheckOut').prop('disabled', false);
                            $('#btnAbsent').prop('disabled', true);

                            $('#breakArea').removeClass('hidden');
                            $('#resumeArea').addClass('hidden');
                            $('#checkOutArea').removeClass('hidden');

                            // Get resume time
                            const resumeTime = lastResume ? lastResume.time : '';

                            if (resumeTime) {
                                $('#breakTimeDisplay').text('Resumed at ' + resumeTime).removeClass('hidden');
                            }

                            console.log('Resumed from break');
                        } else if (data.last_action === 'checkout') {

                            $('#btnCheckIn').prop('disabled', true);
                            $('#btnBreak').prop('disabled', true);
                            $('#btnResume').prop('disabled', true);
                            $('#btnCheckOut').prop('disabled', true);
                            $('#btnAbsent').prop('disabled', true);

                            $('#checkInArea, #checkOutArea, #breakArea, #resumeArea').addClass('hidden');

                            $('#checkOutTimeDisplay')
                                .text('You checked out')
                                .removeClass('hidden');


                            // Show checkout message
                            $('#checkOutTimeDisplay').text('You checked out').removeClass('hidden');

                            console.log('Check Out Data:', data.check_out);
                            console.log('Last Action:', data.last_action);
                        } else {
                            // No action yet - Show option to Check In or Mark Absent
                            $('#btnCheckIn').prop('disabled', false);
                            $('#btnAbsent').prop('disabled', false);
                            $('#btnBreak').prop('disabled', true);
                            $('#btnResume').prop('disabled', true);
                            $('#btnCheckOut').prop('disabled', true);
                            $('#absentArea').removeClass('hidden');
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load attendance status:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        // On error, enable basic buttons so user can try again
                        $('#btnCheckIn').prop('disabled', false);
                        $('#btnAbsent').prop('disabled', false);
                        $('#btnCheckOut').prop('disabled', true);
                        $('#btnBreak').prop('disabled', true);
                        $('#btnResume').prop('disabled', true);
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.error 
                            ? xhr.responseJSON.error 
                            : 'Failed to load attendance status. Please try again.';
                        showSuccessNotification(errorMsg);
                    }
                });
        }

            // Display attendance log
            function displayAttendanceLog(records) {
                console.log('displayAttendanceLog called with records:', records);

                if (!records || records.length === 0) {
                    console.log('No records to display');
                    return;
                }

                let html = '';
                records.forEach((record, idx) => {
                    console.log(`Record ${idx}:`, record);

                    // Handle both 'type' and 'action' fields for compatibility
                    const recordType = record.type || record.action;
                    const time = record.time;

                    console.log(`Record type: ${recordType}, Time: ${time}`);

                    let displayLabel, icon, bgColor, iconColor;

                    if (recordType === 'checkin') {
                        displayLabel = 'Check In';
                        icon = '✓';
                        bgColor = 'bg-emerald-50';
                        iconColor = 'text-emerald-600';
                    } else if (recordType === 'break') {
                        displayLabel = 'Break Started';
                        icon = '⏸';
                        bgColor = 'bg-amber-50';
                        iconColor = 'text-amber-600';
                    } else if (recordType === 'resume') {
                        displayLabel = 'Resumed Work';
                        icon = '▶';
                        bgColor = 'bg-purple-50';
                        iconColor = 'text-purple-600';
                    } else {
                        displayLabel = 'Check Out';
                        icon = '✕';
                        bgColor = 'bg-blue-50';
                        iconColor = 'text-blue-600';
                    }

                    html += `
                    <div class="flex items-center gap-4 p-3 ${bgColor} rounded-xl border-l-4 ${recordType === 'checkin' ? 'border-emerald-500' : 'border-blue-500'}">
                        <div class="text-2xl font-bold ${iconColor}">${icon}</div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-600 uppercase tracking-wider">${displayLabel}</p>
                            <p class="text-lg font-black text-slate-800">${time}</p>
                        </div>
                    </div>
                `;
                });

                console.log('Generated HTML:', html);
                $('#logEntries').html(html);
                $('#attendanceLog').removeClass('hidden');
                console.log('Log visible:', $('#attendanceLog').is(':visible'));
            }

            // Break Timer Function
            let breakTimerInterval = null;

            function startBreakTimer(records) {
                // Find the last break record to get break start time
                if (!records || records.length === 0) return;

                const lastBreak = [...records].reverse().find(r => r.type === 'break');
                if (!lastBreak) return;

                // Clear existing timer if any
                if (breakTimerInterval) clearInterval(breakTimerInterval);

                // Parse break start time
                const breakStartStr = lastBreak.time; // Format: "12:45 PM"
                const [time, period] = breakStartStr.split(' ');
                const [hours, minutes] = time.split(':');
                let breakHours = parseInt(hours);
                if (period === 'PM' && breakHours !== 12) breakHours += 12;
                if (period === 'AM' && breakHours === 12) breakHours = 0;

                const breakStartDate = new Date();
                breakStartDate.setHours(breakHours, parseInt(minutes), 0);

                // Update timer every second
                const updateTimer = () => {
                    const now = new Date();
                    const diffMs = now - breakStartDate;
                    const diffMins = Math.floor(diffMs / 60000);
                    const secs = Math.floor((diffMs % 60000) / 1000);

                    const timerText = `${diffMins}m ${secs}s on break`;
                    $('#resumeTimeDisplay').text(`Started at ${breakStartStr} • ${timerText}`).removeClass('hidden');
                };

                updateTimer();
                breakTimerInterval = setInterval(updateTimer, 1000);
            }

            // 2. Attendance SUBMIT karne ka function
            let isSubmitting = false; // Flag to prevent double submission

            function submitAttendance(actionType) {
                let buttonSelector = '';

                // Get the button being clicked
                if (actionType === 'checkin') {
                    buttonSelector = '#btnCheckIn';
                } else if (actionType === 'checkout') {
                    buttonSelector = '#btnCheckOut';
                } else if (actionType === 'break') {
                    buttonSelector = '#btnBreak';
                } else if (actionType === 'resume') {
                    buttonSelector = '#btnResume';
                } else if (actionType === 'absent') {
                    buttonSelector = '#btnAbsent';
                }

                // Check if button is disabled or already submitting
                if ((buttonSelector && $(buttonSelector).prop('disabled')) || isSubmitting) {
                    return; // Prevent action if button is disabled or already submitting
                }

                // Immediately disable button to prevent double submission
                if (buttonSelector) {
                    $(buttonSelector).prop('disabled', true);
                }

                // Set submission flag
                isSubmitting = true;

                $.ajax({
                    url: "{{ route('admin.attendance.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        employee_id: currentEmp,
                        attendance_date: currentDate,
                        action: actionType,
                        absence_reason: actionType === 'absent' ? $('#absenceReasonInput').val() : null,
                        policy_id: actionType === 'absent' ? $('#policySelect').val() || null : null
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        if (response.status === 'success') {
                            // Show success message based on action
                            let actionText = '';
                            if (actionType === 'checkin') {
                                actionText = 'Checked In Successfully ✓';
                            } else if (actionType === 'checkout') {
                                actionText = 'Checked Out Successfully ✓';
                            } else if (actionType === 'break') {
                                actionText = 'Break Started ⏸';
                            } else if (actionType === 'resume') {
                                actionText = 'Resumed Work ▶';
                            } else {
                                actionText = 'Marked Absent';
                            }

                            // Show toast notification instead of alert
                            console.log('Action successful:', actionText);
                            showSuccessNotification(actionText);

                            // Reset submission flag immediately so other buttons can be clicked
                            isSubmitting = false;

                            // Re-fetch and update modal without closing it
                            // The GET callback will set correct button states based on server data
                            setTimeout(() => {
                                openAttendanceModal(currentEmp, $('#modalEmpName').text(), currentDate);
                            }, 300);
                        } else if (response.checkoutDisabled) {
                            // Handle checkout disabled error
                            alert(
                                'Checkout is disabled after 12:00 AM. Please try again after midnight passes.'
                            );
                            closeModal();
                        }
                    },
                    error: function(xhr) {
                        console.log('Error response:', xhr);
                        console.log('Status:', xhr.status);
                        console.log('Response JSON:', xhr.responseJSON);

                        let errorMsg = 'Something went wrong. Please try again.';
                        const response = xhr.responseJSON;

                        if (response) {
                            // Handle validation errors (422)
                            if (xhr.status === 422 && response.errors) {
                                errorMsg = 'Validation error:\n';
                                Object.keys(response.errors).forEach(field => {
                                    errorMsg += response.errors[field][0] + '\n';
                                });
                            }
                            // Handle checkout disabled error (403)
                            else if (response.checkoutDisabled) {
                                errorMsg =
                                    'Checkout is disabled after 12:00 AM. Please try again after midnight passes.';
                            }
                            // Handle already on break error
                            else if (response.alreadyOnBreak) {
                                $('#breakActiveMsg').removeClass('hidden');
                                errorMsg =
                                    'You are already on break. Please resume work first before taking another break.';
                            }
                            // Handle other error messages
                            else if (response.message) {
                                errorMsg = response.message;
                            }
                        }

                        alert(errorMsg);

                        // Reset submission flag on error
                        isSubmitting = false;
                    }
                });
            }

            // 3. Modal CLOSE karne ka function
            function closeModal() {
                $('#actionModal').addClass('hidden');
                // Reload page to update table
                location.reload();
            }

            // Show success toast notification
            function showSuccessNotification(message) {
                const toast = $(
                    `<div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg animate-in fade-in slide-in-from-bottom-2 z-[200]">${message}</div>`
                );
                $('body').append(toast);
                setTimeout(() => {
                    toast.remove();
                }, 2000);
            }
    </script>
@endsection
