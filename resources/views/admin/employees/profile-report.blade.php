@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] p-6 lg:p-10 antialiased font-sans">
    
    <div class="max-w-8xl mx-auto flex flex-col md:flex-row md:items-center justify-between mb-8 gap-6">
        <div>
            <nav class="flex mb-2 space-x-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                <a href="{{ route('admin.employees.index') }}" class="hover:text-indigo-600">Employees</a>
                <span class="text-slate-300">/</span>
                <span class="text-indigo-600">Profile Report</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $employee->name }}</h1>
            <p class="text-slate-500 mt-1 text-sm font-medium">Employee Profile Report - Ref: {{ $employee->employee_id }}</p>
        </div>
        
        <a href="{{ route('admin.employees.index') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-slate-100 text-slate-700 text-[11px] font-bold uppercase tracking-[0.15em] rounded-lg hover:bg-slate-200 transition-all shadow-md active:scale-95">
            <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
            Back to List
        </a>
    </div>

    <div class="max-w-8xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ activeTab: '{{ $activeTab ?? 'personal' }}' }">
        <!-- Tab Navigation -->
        <div class="border-b border-slate-200 bg-slate-50/50">
            <div class="flex items-center px-8">
                <button @click="activeTab = 'personal'" 
                        :class="activeTab === 'personal' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
                        class="py-4 px-4 text-sm font-bold uppercase tracking-wider transition-colors border-b-2 border-transparent">
                    <i class="fas fa-user mr-2"></i>Personal Information
                </button>
                <button @click="activeTab = 'attendance'" 
                        :class="activeTab === 'attendance' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
                        class="py-4 px-4 text-sm font-bold uppercase tracking-wider transition-colors border-b-2 border-transparent">
                    <i class="fas fa-calendar-check mr-2"></i>Attendance
                </button>
                <button @click="activeTab = 'absents'" 
                        :class="activeTab === 'absents' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
                        class="py-4 px-4 text-sm font-bold uppercase tracking-wider transition-colors border-b-2 border-transparent">
                    <i class="fas fa-calendar-times mr-2"></i>Absents
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-8">     
            <!-- Personal Information Tab -->
            <div x-show="activeTab === 'personal'" class="space-y-6">
                <!-- Employee Picture & Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- Profile Picture -->
                    <div class="md:col-span-1 flex flex-col items-center">
                        <div class="relative mb-4">
                            @if($employee->employee_picture)
                                <img src="{{ asset('employee-profile/' . $employee->employee_picture) }}" 
                                     class="w-32 h-32 rounded-2xl object-cover border-4 border-slate-100 shadow-md">
                            @else
                                <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 flex items-center justify-center text-white text-5xl font-bold shadow-lg">
                                    {{ substr($employee->name ?? 'N', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 text-center">{{ $employee->name ?? 'N/A' }}</h2>
                        <p class="text-xs font-semibold text-indigo-600 uppercase mt-1">Ref: {{ $employee->employee_id ?? 'N/A' }}</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-4 border border-emerald-200">
                            <span class="block text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Designation</span>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->designation_id ?? 'Unassigned' }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 border border-blue-200">
                            <span class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider">Department</span>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->employee_department_id ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl p-4 border border-amber-200">
                            <span class="block text-[10px] font-bold text-amber-600 uppercase tracking-wider">Joining Date</span>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') : '---' }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-4 border border-slate-200">
                            <span class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider">Account Status</span>
                            <p class="text-sm font-bold mt-2">
                                @if($employee->is_resigned)
                                    <span class="inline-flex items-center px-2 py-1 bg-rose-100 text-rose-700 rounded text-xs font-bold">
                                        <i class="fas fa-user-slash mr-1"></i>Resigned
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-address-card text-indigo-600 mr-2"></i>Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                            <p class="text-sm font-bold text-slate-900 mt-2 break-all">{{ $employee->email ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mobile Number</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->mobile_number ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Date of Birth</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('M d, Y') : '---' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-id-card text-indigo-600 mr-2"></i>Personal Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">CNIC / ID Number</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->cnic ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Father's Name</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->father_name ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Age</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->age ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Education</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->education ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Number</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->account_number ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tenure</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->tenure ?? '---' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-briefcase text-blue-600 mr-2"></i>Employment Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Project Name</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->project_name ?? 'Not Assigned' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Project ID</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->project_id ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Employee Status</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">
                                <span class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-bold">
                                    {{ $employee->employee_status ?? '---' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Deployment Information -->
                <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-map-location-dot text-indigo-600 mr-2"></i>Deployment Logistics
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Area</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->deployment_area ?? '---' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Site</label>
                            <p class="text-sm font-bold text-slate-900 mt-2">{{ $employee->deployment_site ?? '---' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Quota & Leave Balance -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white">
                    <h3 class="text-sm font-bold text-slate-100 mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-emerald-400 mr-2"></i>Leave & Attendance Balance
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="flex items-center justify-between bg-white/10 rounded-xl p-4 border border-white/10">
                            <div>
                                <p class="text-xs font-bold text-slate-300 uppercase">Total Days</p>
                                <p class="text-2xl font-bold text-blue-400 mt-1">{{ $employee->total_leaves ?? 0 }}</p>
                            </div>
                            <i class="fas fa-calendar text-3xl opacity-20"></i>
                        </div>
                        <div class="flex items-center justify-between bg-white/10 rounded-xl p-4 border border-white/10">
                            <div>
                                <p class="text-xs font-bold text-slate-300 uppercase">Used Days</p>
                                <p class="text-2xl font-bold text-amber-400 mt-1">{{ $employee->used_leaves ?? 0 }}</p>
                            </div>
                            <i class="fas fa-calendar-xmark text-3xl opacity-20"></i>
                        </div>
                        <div class="flex items-center justify-between bg-white/10 rounded-xl p-4 border border-white/10">
                            <div>
                                <p class="text-xs font-bold text-slate-300 uppercase">Available Days</p>
                                <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $employee->remaining_leaves ?? 0 }}</p>
                            </div>
                            <i class="fas fa-umbrella-beach text-3xl opacity-20"></i>
                        </div>
                        <div class="flex items-center justify-between bg-white/10 rounded-xl p-4 border border-white/10">
                            <div>
                                <p class="text-xs font-bold text-slate-300 uppercase">Leave Status</p>
                                <p class="text-sm font-bold text-slate-200 mt-1">{{ $employee->leave_status ?? '---' }}</p>
                            </div>
                            <i class="fas fa-check-circle text-3xl opacity-20"></i>
                        </div>
                    </div>
                </div>

                <!-- Admin Remarks -->
                @if($employee->comment)
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-comment-dots text-indigo-600 mr-2"></i>Admin Remarks
                    </h3>
                    <div class="text-sm text-slate-700 leading-relaxed bg-white border border-slate-100 rounded-xl p-4 italic">
                        "{{ $employee->comment }}"
                    </div>
                </div>
                @endif

                <!-- System Information -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <span><i class="fas fa-calendar-plus mr-2 text-slate-300"></i>Created: {{ $employee->created_at ? $employee->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                    <span><i class="fas fa-clock mr-2 text-slate-300"></i>Updated: {{ $employee->updated_at ? $employee->updated_at->diffForHumans() : 'N/A' }}</span>
                </div>
            </div>

            <!-- Attendance Tab -->
            <div x-show="activeTab === 'attendance'" class="space-y-4">
                <!-- Date Filter -->
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <input type="hidden" name="tab" value="attendance">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">From Date</label>
                            <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">To Date</label>
                            <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-indigo-700 transition-all active:scale-95">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Attendance Records</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase">{{ $attendances->count() }} records</span>
                </div>
                @if($attendances->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Date</th>
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Status</th>
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Shift Timing</th>
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Working Duration</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($attendances as $record)
                                <tr class="group hover:bg-blue-50/30 transition-all">
                                    <td class="p-5">
                                        <span class="text-sm font-bold text-slate-600 italic opacity-80">
                                            {{ date('d M, Y (l)', strtotime($record->attendance_date)) }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        @if ($record->attendance == 'Present')
                                            <span class="px-3 py-1 bg-emerald-100/70 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                <i class="fas fa-check-circle mr-1"></i>Present
                                            </span>
                                        @else
                                            <div class="flex flex-col gap-2">
                                                <span class="px-3 py-1 bg-rose-100/70 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-wider inline-block w-fit">
                                                    <i class="fas fa-times-circle mr-1"></i>Absent
                                                </span>
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
                                                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-wider mb-0.5">In</span>
                                                <span class="text-xs font-black text-slate-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                                                    {{ $record->check_in ? date('h:i A', strtotime($record->check_in)) : '--:--' }}
                                                </span>
                                            </div>

                                            {{-- Arrow Icon --}}
                                            <i class="fas fa-long-arrow-alt-right text-slate-300 mt-4"></i>

                                            {{-- Check Out Block --}}
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-blue-500 uppercase tracking-wider mb-0.5">Out</span>
                                                <span class="text-xs font-black text-slate-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                                    {{ $record->check_out ? date('h:i A', strtotime($record->check_out)) : '--:--' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        @php
                                            $tz = 'America/Denver';
                                            $workingDuration = '0h 0m 0s';
                                            $breakDuration = '--:--';
                                            
                                            $entries = $record->entries;
                                            if (is_string($entries)) {
                                                $entries = json_decode($entries, true);
                                            }
                                            if (!is_array($entries)) {
                                                $entries = [];
                                            }
                                            
                                            if (!empty($entries)) {
                                                $workingSeconds = 0;
                                                $breakSeconds = 0;
                                                $lastCheckinTime = null;
                                                $breakStartTime = null;
                                                
                                                foreach ($entries as $entry) {
                                                    if ($entry['type'] === 'checkin') {
                                                        $lastCheckinTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                    } elseif ($entry['type'] === 'break' && $lastCheckinTime) {
                                                        $breakStartTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                        $workingSeconds += $lastCheckinTime->diffInSeconds($breakStartTime);
                                                        $lastCheckinTime = null;
                                                    } elseif ($entry['type'] === 'resume' && $breakStartTime) {
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
                                                        if ($breakStartTime) {
                                                            $checkoutTime = \Carbon\Carbon::parse($entry['time'], $tz);
                                                            $breakSeconds += $breakStartTime->diffInSeconds($checkoutTime);
                                                            $breakStartTime = null;
                                                        }
                                                    }
                                                }
                                                
                                                $workingHours = intval($workingSeconds / 3600);
                                                $workingMins = intval(($workingSeconds % 3600) / 60);
                                                $workingSecs = intval($workingSeconds % 60);
                                                
                                                $breakHours = intval($breakSeconds / 3600);
                                                $breakMins = intval(($breakSeconds % 3600) / 60);
                                                $breakSecs = intval($breakSeconds % 60);
                                                
                                                $workingDuration = "{$workingHours}h {$workingMins}m {$workingSecs}s";
                                                $breakDuration = $breakHours > 0 || $breakMins > 0 || $breakSecs > 0 ? "{$breakHours}h {$breakMins}m {$breakSecs}s" : "--:--";
                                            } elseif ($record->working_hours > 0 || $record->total_break_hours > 0) {
                                                $workingDuration = $record->working_hours ?? '0h 0m 0s';
                                                $breakDuration = $record->total_break_hours ?? '--:--';
                                            }
                                        @endphp
                                        <div class="flex flex-col gap-2">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Working</span>
                                                <span class="text-xs font-black text-slate-700 bg-slate-50 px-2 py-1 rounded-md border border-slate-100">
                                                    {{ $workingDuration }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Break</span>
                                                <span class="text-xs font-black text-slate-700 bg-slate-50 px-2 py-1 rounded-md border border-slate-100">
                                                    {{ $breakDuration }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-2xl border border-slate-200">
                    <i class="fas fa-inbox text-slate-200 text-4xl mb-4"></i>
                    <p class="text-slate-500 font-medium">No attendance records found in this date range</p>
                </div>
                @endif
            </div>

            <!-- Absents Tab -->
            <div x-show="activeTab === 'absents'" class="space-y-4">
                <!-- Date Filter -->
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <input type="hidden" name="tab" value="absents">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">From Date</label>
                            <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">To Date</label>
                            <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-indigo-700 transition-all active:scale-95">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Absent Records</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase">{{ $absents->count() }} absences</span>
                </div>
                @if($absents->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Date</th>
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Status</th>
                                    <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.1em]">Policy & Reason</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($absents as $record)
                                <tr class="group hover:bg-rose-50/30 transition-all">
                                    <td class="p-5">
                                        <span class="text-sm font-bold text-slate-600 italic opacity-80">
                                            {{ date('d M, Y (l)', strtotime($record->attendance_date)) }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <span class="px-3 py-1 bg-rose-100/70 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-wider inline-block">
                                            <i class="fas fa-times-circle mr-1"></i>Absent
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex flex-col gap-2">
                                            @if($record->policy)
                                                <p class="text-xs font-semibold text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-200">
                                                    <span class="font-black">Policy: </span>{{ $record->policy->name }}
                                                </p>
                                            @else
                                                <p class="text-xs font-semibold text-slate-500 italic">No policy applied</p>
                                            @endif
                                            
                                            @if($record->absence_reason)
                                                <p class="text-xs font-semibold text-rose-700 bg-rose-50 p-2 rounded-lg border border-rose-200">
                                                    <span class="font-black">Reason: </span>{{ $record->absence_reason }}
                                                </p>
                                            @else
                                                <p class="text-xs font-semibold text-slate-500 italic">No reason provided</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-2xl border border-slate-200">
                    <i class="fas fa-check-circle text-emerald-200 text-4xl mb-4"></i>
                    <p class="text-slate-500 font-medium">No absences found in this date range</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
