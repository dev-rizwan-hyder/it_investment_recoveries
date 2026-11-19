@extends('layouts.admin')

@section('content')
    <div class="p-8 bg-slate-50/50 min-h-screen">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Attendance Detail Logs</h1>
                <p class="text-sm text-slate-500 font-medium">Review and manage detailed employee time logs.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.attendance') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all active:scale-95">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                    Back to Sheet
                </a>
            </div>
        </div>

        {{-- Stats Cards (Optional but Professional) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Records</p>
                <h2 class="text-2xl font-black text-slate-800 mt-1">{{ $attendances->total() }}</h2>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-black text-emerald-400 uppercase tracking-widest">Present Today</p>
                <h2 class="text-2xl font-black text-slate-800 mt-1">
                    {{ $attendances->where('attendance', 'Present')->count() }}</h2>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-black text-rose-400 uppercase tracking-widest">Absents Recorded</p>
                <h2 class="text-2xl font-black text-slate-800 mt-1">
                    {{ $attendances->where('attendance', 'Absent')->count() }}</h2>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
            <form method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px] relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <select name="employee_id"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px] relative">
                    <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="date" name="date" value="{{ $selectedDate }}"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#153d77] text-white rounded-xl font-black text-sm hover:bg-blue-800 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                    Apply Filters
                </button>
                <a href="{{ route('admin.attendance.list') }}"
                    class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </form>
        </div>

        {{-- Main Table --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Employee
                                Details</th>
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Date</th>
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Status</th>
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Shift Timing
                            </th>
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Working
                                Duration</th>
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Break Duration</th>
                            <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em] text-right">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($attendances as $record)
                            <tr class="group hover:bg-blue-50/30 transition-all">
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-xs">
                                            {{ substr($record->employee?->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="font-bold text-slate-700 tracking-tight">{{ $record->employee?->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="text-sm font-bold text-slate-600 italic opacity-80">{{ date('d M, Y', strtotime($record->attendance_date)) }}</span>
                                </td>
                                <td class="p-5">
                                    @if ($record->attendance == 'Present')
                                        <span
                                            class="px-3 py-1 bg-emerald-100/70 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wider">Present</span>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            <span
                                                class="px-3 py-1 bg-rose-100/70 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-wider inline-block w-fit">Absent</span>
                                            @if ($record->policy)
                                                <p class="text-xs font-semibold text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-200">
                                                    <span class="font-black">Policy: </span>{{ $record->policy->name }}
                                                </p>
                                            @endif
                                            @if ($record->absence_reason)
                                                <p class="text-xs font-semibold text-rose-700 bg-rose-50 p-2 rounded-lg border border-rose-200">
                                                    <span class="font-black">Reason: </span>{{ $record->absence_reason }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="p-5">
                                    <div class="flex items-center gap-4">
                                        {{-- Check In Block --}}
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] font-black text-emerald-500 uppercase tracking-wider mb-0.5">In</span>
                                            <span
                                                class="text-xs font-black text-slate-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                                                {{ $record->check_in ? date('h:i A', strtotime($record->check_in)) : '--:--' }}
                                            </span>
                                        </div>

                                        {{-- Arrow Icon --}}
                                        <i class="fas fa-long-arrow-alt-right text-slate-300 mt-4"></i>

                                        {{-- Check Out Block --}}
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] font-black text-blue-500 uppercase tracking-wider mb-0.5">Out</span>
                                            <span
                                                class="text-xs font-black text-slate-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                                {{ $record->check_out ? date('h:i A', strtotime($record->check_out)) : '--:--' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    @php
                                        $tz = 'America/Denver';
                                        $workingHours = 0;
                                        $breakHours = 0;
                                        $workingMins = 0;
                                        $breakMins = 0;
                                        $workingDuration = '0h 0m 0s';
                                        $breakDuration = '--:--';
                                        
                                        // Helper function to decode entries
                                        $entries = $record->entries;
                                        if (is_string($entries)) {
                                            $entries = json_decode($entries, true);
                                        }
                                        if (!is_array($entries)) {
                                            $entries = [];
                                        }
                                        
                                        // Calculate from entries (using seconds for accuracy)
                                        if (!empty($entries)) {
                                            $workingSeconds = 0;
                                            $breakSeconds = 0;
                                            $lastCheckinTime = null;
                                            $breakStartTime = null;
                                            
                                            foreach ($entries as $entry) {
                                                if ($entry['type'] === 'checkin') {
                                                    $lastCheckinTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                } elseif ($entry['type'] === 'break' && $lastCheckinTime) {
                                                    // Break started
                                                    $breakStartTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                    $workingSeconds += $lastCheckinTime->diffInSeconds($breakStartTime);
                                                    $lastCheckinTime = null;
                                                } elseif ($entry['type'] === 'resume' && $breakStartTime) {
                                                    // Break ended
                                                    $resumeTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                    $breakSeconds += $breakStartTime->diffInSeconds($resumeTime);
                                                    $breakStartTime = null;
                                                    $lastCheckinTime = $resumeTime;
                                                } elseif ($entry['type'] === 'checkout') {
                                                    if ($lastCheckinTime) {
                                                        $checkoutTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                        $workingSeconds += $lastCheckinTime->diffInSeconds($checkoutTime);
                                                        $lastCheckinTime = null;
                                                    }
                                                    // If still on break at checkout, calculate remaining break time
                                                    if ($breakStartTime) {
                                                        $checkoutTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                        $breakSeconds += $breakStartTime->diffInSeconds($checkoutTime);
                                                        $breakStartTime = null;
                                                    }
                                                }
                                            }
                                            
                                            // Convert seconds to hours:minutes:seconds
                                            $workingHours = intval($workingSeconds / 3600);
                                            $workingMins = intval(($workingSeconds % 3600) / 60);
                                            $workingSecs = intval($workingSeconds % 60);
                                            
                                            $breakHours = intval($breakSeconds / 3600);
                                            $breakMins = intval(($breakSeconds % 3600) / 60);
                                            $breakSecs = intval($breakSeconds % 60);
                                            
                                            $workingDuration = "{$workingHours}h {$workingMins}m {$workingSecs}s";
                                            $breakDuration = $breakHours > 0 || $breakMins > 0 || $breakSecs > 0 ? "{$breakHours}h {$breakMins}m {$breakSecs}s" : "--:--";
                                        }
                                        // Override with database values if they exist and are greater than calculated
                                        elseif ($record->working_hours > 0 || $record->total_break_hours > 0) {
                                            if ($record->working_hours > 0) {
                                                $totalWorkingSeconds = $record->working_hours * 3600;
                                                $workingHours = intval($totalWorkingSeconds / 3600);
                                                $workingMins = intval(($totalWorkingSeconds % 3600) / 60);
                                                $workingSecs = intval($totalWorkingSeconds % 60);
                                                $workingDuration = "{$workingHours}h {$workingMins}m {$workingSecs}s";
                                            }
                                            if ($record->total_break_hours > 0) {
                                                $totalBreakSeconds = $record->total_break_hours * 3600;
                                                $breakHours = intval($totalBreakSeconds / 3600);
                                                $breakMins = intval(($totalBreakSeconds % 3600) / 60);
                                                $breakSecs = intval($totalBreakSeconds % 60);
                                                $breakDuration = "{$breakHours}h {$breakMins}m {$breakSecs}s";
                                            }
                                        }
                                    @endphp

                                    <span
                                        class="inline-flex items-center text-xs font-bold 
                                        {{ $workingHours >= 8 ? 'text-emerald-600 bg-emerald-50' : 'text-blue-600 bg-blue-50' }} 
                                        px-3 py-1.5 rounded-xl border border-current opacity-80">
                                        <i class="fas fa-clock mr-1.5 text-[10px]"></i>
                                        {{ $workingDuration }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="inline-flex items-center text-xs font-bold 
                                        {{ ($breakHours > 0 || $breakMins > 0) ? 'text-amber-600 bg-amber-50' : 'text-slate-400 bg-slate-50' }} 
                                        px-3 py-1.5 rounded-xl border border-current opacity-80">
                                        <i class="fas fa-pause-circle mr-1.5 text-[10px]"></i>
                                        {{ $breakDuration }}
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <button onclick="editRecord({{ $record->id }})"
                                            class="p-2.5 bg-white border border-slate-100 text-amber-500 rounded-xl shadow-sm hover:shadow-md hover:bg-amber-500 hover:text-white transition-all">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <form action="{{ route('admin.attendance.destroy', $record->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Archive this record?')"
                                                class="p-2.5 bg-white border border-slate-100 text-rose-500 rounded-xl shadow-sm hover:shadow-md hover:bg-rose-500 hover:text-white transition-all">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50/50 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>

    {{-- Professional Edit Modal (Same as Screen Shot Look) --}}
    <div id="editModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div
            class="relative bg-white rounded-[2rem] p-8 w-full max-w-sm shadow-2xl transform transition-all animate-in zoom-in duration-200">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-edit text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800">Update Entry</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manual Time Correction</p>
            </div>

            <form id="editForm" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div class="group">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Attendance
                        Status</label>
                    <select name="attendance" id="edit_status"
                        class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:border-blue-500 focus:bg-white transition-all outline-none">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="group">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Check
                            In</label>
                        <input type="time" name="check_in" id="edit_check_in"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:border-blue-500 focus:bg-white transition-all outline-none">
                    </div>
                    <div class="group">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Check
                            Out</label>
                        <input type="time" name="check_out" id="edit_check_out"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:border-blue-500 focus:bg-white transition-all outline-none">
                    </div>
                </div>

                <div class="pt-4 flex flex-col gap-3">
                    <button type="submit"
                        class="w-full py-4 bg-[#153d77] text-white rounded-2xl font-black text-sm shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95">
                        Save Changes
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="w-full py-3 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] hover:text-slate-600 transition-all">
                        Dismiss
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Smooth Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Animation for Table Rows */
        tbody tr {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        tbody tr:nth-child(1) {
            animation-delay: 0.1s;
        }

        tbody tr:nth-child(2) {
            animation-delay: 0.2s;
        }

        tbody tr:nth-child(3) {
            animation-delay: 0.3s;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function editRecord(id) {
        // Project ka base path sahi se uthane ke liye
        let baseUrl = "{{ url('/') }}"; 
        let editUrl = baseUrl + '/admin/attendance/' + id + '/edit';
        let updateUrl = baseUrl + '/admin/attendance/' + id;

        // Modal foran dikhayen aur loading state dein
        $('#editModal').removeClass('hidden').addClass('flex');
        $('#display_name').text('Loading...');

        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(data) {
                // Form action aur display fields update karein
                $('#editForm').attr('action', updateUrl);
                $('#display_id').text(data.id);
                $('#display_name').text(data.employee ? data.employee.name : 'Unknown');
                $('#display_date').text(data.attendance_date);
                
                // Status aur Time set karein
                $('#edit_status').val(data.attendance);
                
                // Input type="time" ko 00:00 format chahiye hota hai
                if(data.check_in) {
                    $('#edit_check_in').val(data.check_in.substring(0, 5));
                } else {
                    $('#edit_check_in').val('');
                }
                
                if(data.check_out) {
                    $('#edit_check_out').val(data.check_out.substring(0, 5));
                } else {
                    $('#edit_check_out').val('');
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                alert("Error: Could not fetch record. Check console for details.");
                closeEditModal();
            }
        });
    }

    function closeEditModal() {
        $('#editModal').addClass('hidden').removeClass('flex');
    }
</script>
@endsection
