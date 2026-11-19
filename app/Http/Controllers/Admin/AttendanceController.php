<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Policy;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        Carbon::setLocale('en');
        $tz = 'America/Denver';

        $nowInDenver = Carbon::now($tz);

        // Agar user ne month filter nahi kiya toh Denver ka current month uthayen
        $selectedMonth = $request->input('month', $nowInDenver->format('Y-m'));

        $startDate = Carbon::createFromFormat('Y-m', $selectedMonth, $tz)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $selectedMonth, $tz)->endOfMonth();

        $employees = Employee::where('is_resigned', 0)
            ->with([
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('attendance_date', [
                        $startDate->format('Y-m-d'),
                        $endDate->format('Y-m-d')
                    ]);
                }
            ])
            ->get();

        $daysInMonth = $startDate->daysInMonth;

        // Yeh line "Today" highlight ko Friday (March 6) par rakhegi
        $today = $nowInDenver->format('Y-m-d');

        $isCurrentMonth = ($selectedMonth === $nowInDenver->format('Y-m'));

        // Get all policies for the dropdown
        $policies = Policy::all();

        return view('admin.attendance.index', compact(
            'employees',
            'selectedMonth',
            'daysInMonth',
            'today',
            'isCurrentMonth',
            'policies'
        ));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'employee_id' => 'required|integer|exists:employees,id',
                'attendance_date' => 'required|date_format:Y-m-d',
                'action' => 'required|in:checkin,checkout,absent,break,resume',
                'absence_reason' => 'nullable|string|max:500',
                'policy_id' => 'nullable|integer|exists:policies,id',
            ]);

            $tz = 'America/Denver';
            $nowInDenver = \Carbon\Carbon::now($tz);
            $currentTime = $nowInDenver->format('H:i:s');
            $currentHour = (int)$nowInDenver->format('H');

            // Check if trying to checkout after 12:00 AM (hour is less than 1 means 00:00-00:59)
            if ($data['action'] === 'checkout' && $currentHour === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Checkout is disabled after 12:00 AM. Please checkout before midnight.',
                    'checkoutDisabled' => true
                ], 403);
            }

            // Find or create attendance record for this employee on this date
            $attendance = Attendance::where('employee_id', $data['employee_id'])
                ->where('attendance_date', $data['attendance_date'])
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id = $data['employee_id'];
                $attendance->attendance_date = $data['attendance_date'];
                $attendance->entries = [];
                $attendance->attendance = 'Present'; // Default to Present
            }

            // Get current entries (fix for indirect modification issue)
            $entries = is_array($attendance->entries) ? $attendance->entries : [];

            if ($data['action'] === 'checkin') {
                // Add checkin entry
                $entries[] = [
                    'type' => 'checkin',
                    'time' => $currentTime
                ];
                $attendance->entries = $entries;
                $attendance->attendance = 'Present';
                // Set check_in if not already set (for backward compatibility)
                if (!$attendance->check_in) {
                    $attendance->check_in = $currentTime;
                }
            } elseif ($data['action'] === 'checkout') {
                // Add checkout entry
                $entries[] = [
                    'type' => 'checkout',
                    'time' => $currentTime
                ];
                $attendance->entries = $entries;
                // Set check_out to latest checkout time
                $attendance->check_out = $currentTime;

                // Calculate working hours and break hours from entries
                $workingMinutes = 0;
                $breakMinutes = 0;
                $lastCheckinTime = null;
                $breakStartTime = null;

                foreach ($entries as $entry) {
                    if ($entry['type'] === 'checkin') {
                        $lastCheckinTime = \Carbon\Carbon::parse($entry['time'], $tz);
                        if ($breakStartTime) {
                            // Break just ended, add break duration
                            $breakEnd = $lastCheckinTime;
                            $breakMinutes += $breakStartTime->diffInMinutes($breakEnd);
                            $breakStartTime = null;
                        }
                    } elseif ($entry['type'] === 'break' && $lastCheckinTime) {
                        // Break started
                        $breakStartTime = \Carbon\Carbon::parse($entry['time'], $tz);
                        // Add working time from last checkin to break start
                        $workingMinutes += $lastCheckinTime->diffInMinutes($breakStartTime);
                        $lastCheckinTime = null;
                    } elseif ($entry['type'] === 'resume') {
                        $breakStartTime = null;
                        $lastCheckinTime = \Carbon\Carbon::parse($entry['time'], $tz);
                    } elseif ($entry['type'] === 'checkout') {
                        if ($lastCheckinTime) {
                            $checkoutTime = \Carbon\Carbon::parse($entry['time'], $tz);
                            $workingMinutes += $lastCheckinTime->diffInMinutes($checkoutTime);
                            $lastCheckinTime = null;
                        }
                    }
                }

                // Store calculated hours
                if ($workingMinutes > 0) {
                    $attendance->working_hours = round($workingMinutes / 60, 2);
                }
                if ($breakMinutes > 0) {
                    $attendance->total_break_hours = round($breakMinutes / 60, 2);
                }
                
                // stay_hours is total time (working + break)
                $totalMinutes = $workingMinutes + $breakMinutes;
                if ($totalMinutes > 0) {
                    $attendance->stay_hours = round($totalMinutes / 60, 2);
                }
            } elseif ($data['action'] === 'break') {
                // Check if already on break
                if (count($entries) > 0) {
                    $lastEntry = end($entries);
                    if ($lastEntry['type'] === 'break') {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'You are already on break. Please resume work first before taking another break.',
                            'alreadyOnBreak' => true
                        ], 400);
                    }
                }
                
                // Add break entry and track start time
                $entries[] = [
                    'type' => 'break',
                    'time' => $currentTime
                ];
                $attendance->entries = $entries;
                $attendance->break_start_time = $currentTime;
            } elseif ($data['action'] === 'resume') {
                // Add resume entry and calculate break duration
                $entries[] = [
                    'type' => 'resume',
                    'time' => $currentTime
                ];
                $attendance->entries = $entries;
                $attendance->break_end_time = $currentTime;
                
                // Calculate break duration if break_start_time is set
                if ($attendance->break_start_time) {
                    $breakStart = \Carbon\Carbon::parse($attendance->break_start_time, $tz);
                    $breakEnd = \Carbon\Carbon::parse($currentTime, $tz);
                    $breakMinutes = $breakStart->diffInMinutes($breakEnd);
                    $attendance->total_break_hours = round($breakMinutes / 60, 2);
                }
            } else {
                // Mark as Absent
                $attendance->attendance = 'Absent';
                $attendance->check_in = null;
                $attendance->check_out = null;
                $attendance->stay_hours = null;
                $attendance->entries = [];
                $attendance->absence_reason = $data['absence_reason'] ?? null;
                $attendance->policy_id = $data['policy_id'] ?? null;
            }

            // Save the record - will automatically detect changes
            $attendance->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance updated in Denver Time'
            ]);

        } catch (\Exception $e) {
            \Log::error('Attendance store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatus(Request $request)
    {
        try {
            $tz = 'America/Denver';
            $nowInDenver = \Carbon\Carbon::now($tz);
            $currentHour = (int)$nowInDenver->format('H');

            $attendance = Attendance::where('employee_id', $request->employee_id)
                ->where('attendance_date', $request->attendance_date)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'type' => null,
                    'last_action' => null,
                    'checkoutDisabled' => false,
                    'records' => []
                ]);
            }

            // Determine last action from entries array
            $lastAction = null;
            $records = [];
            
            // Get entries as array to avoid indirect modification issues
            $entriesArray = is_array($attendance->entries) ? $attendance->entries : [];

            if (count($entriesArray) > 0) {
                // Get the last entry
                $lastEntry = end($entriesArray);
                $lastAction = $lastEntry['type']; // 'checkin' or 'checkout'

                // Format records for display
                foreach ($entriesArray as $entry) {
                    try {
                        $time = \Carbon\Carbon::createFromFormat('H:i:s', $entry['time'], $tz)->format('h:i A');
                    } catch (\Exception $e) {
                        // If time format is invalid, use the entry time as-is
                        $time = $entry['time'];
                    }
                    
                    if ($entry['type'] === 'checkin') {
                        $records[] = [
                            'action' => 'Check In',
                            'time' => $time,
                            'type' => 'checkin'
                        ];
                    } elseif ($entry['type'] === 'break') {
                        $records[] = [
                            'action' => 'Break',
                            'time' => $time,
                            'type' => 'break'
                        ];
                    } elseif ($entry['type'] === 'resume') {
                        $records[] = [
                            'action' => 'Resume',
                            'time' => $time,
                            'type' => 'resume'
                        ];
                    } else {
                        $records[] = [
                            'action' => 'Check Out',
                            'time' => $time,
                            'type' => 'checkout'
                        ];
                    }
                }
            }

            // Check if checkout is disabled (after 12:00 AM)
            $checkoutDisabled = $currentHour === 0; // Hour 0 means 00:00-00:59

            // Get check_in and check_out times from the last recorded entries
            // These were already formatted above in the records loop
            $checkInTime = null;
            $checkOutTime = null;

            if (count($records) > 0) {
                // Get first check-in time
                $firstCheckin = collect($records)->firstWhere('type', 'checkin');
                if ($firstCheckin) {
                    $checkInTime = $firstCheckin['time'];
                }
                
                // Get last check-out time
                $lastCheckout = collect($records)->reverse()->firstWhere('type', 'checkout');
                if ($lastCheckout) {
                    $checkOutTime = $lastCheckout['time'];
                }
            }

            return response()->json([
                'type' => $attendance->attendance, // 'Present' or 'Absent'
                'last_action' => $lastAction, // 'checkin' or 'checkout'
                'check_in' => $checkInTime,
                'check_out' => $checkOutTime,
                'checkoutDisabled' => $checkoutDisabled,
                'absence_reason' => $attendance->absence_reason,
                'policy_name' => $attendance->policy ? $attendance->policy->name : null,
                'records' => $records
            ]);
        } catch (\Exception $e) {
            \Log::error('getStatus error: ' . $e->getMessage());
            return response()->json([
                'type' => null,
                'last_action' => null,
                'checkoutDisabled' => false,
                'records' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Calculate working hours and break hours from entries
    private function calculateWorkingHours($entries, $tz = 'America/Denver')
    {
        if (!is_array($entries) || count($entries) === 0) {
            return ['working_hours' => 0, 'break_hours' => 0, 'total_hours' => 0];
        }

        $workingMinutes = 0;
        $breakMinutes = 0;
        $lastCheckinTime = null;
        $breakStartTime = null;

        foreach ($entries as $entry) {
            if ($entry['type'] === 'checkin') {
                $lastCheckinTime = \Carbon\Carbon::parse($entry['time'], $tz);
                if ($breakStartTime) {
                    // Break just ended, add break duration
                    $breakEnd = $lastCheckinTime;
                    $breakMinutes += $breakStartTime->diffInMinutes($breakEnd);
                    $breakStartTime = null;
                }
            } elseif ($entry['type'] === 'break' && $lastCheckinTime) {
                // Break started
                $breakStartTime = \Carbon\Carbon::parse($entry['time'], $tz);
                // Add working time from last checkin to break start
                $workingMinutes += $lastCheckinTime->diffInMinutes($breakStartTime);
                $lastCheckinTime = null;
            } elseif ($entry['type'] === 'resume') {
                $breakStartTime = null;
                $lastCheckinTime = \Carbon\Carbon::parse($entry['time'], $tz);
            } elseif ($entry['type'] === 'checkout') {
                if ($lastCheckinTime) {
                    $checkoutTime = \Carbon\Carbon::parse($entry['time'], $tz);
                    $workingMinutes += $lastCheckinTime->diffInMinutes($checkoutTime);
                    $lastCheckinTime = null;
                }
                // If on break, don't add to working time
            }
        }

        $workingHours = round($workingMinutes / 60, 2);
        $breakHours = round($breakMinutes / 60, 2);
        $totalHours = round(($workingMinutes + $breakMinutes) / 60, 2);

        return [
            'working_hours' => $workingHours,
            'break_hours' => $breakHours,
            'total_hours' => $totalHours
        ];
    }

    // Saari attendance history dekhne ke liye
    public function list(Request $request)
    {
        $query = Attendance::with('employee', 'policy');

        // Filter by Employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Default to Current Day if no date is provided
        $selectedDate = $request->input('date', now('America/Denver')->format('Y-m-d'));
        $query->whereDate('attendance_date', $selectedDate);

        $attendances = $query->orderBy('attendance_date', 'desc')->paginate(20);
        $employees = Employee::where('is_resigned', 0)->get();

        return view('admin.attendance.list', compact('attendances', 'employees', 'selectedDate'));
    }

    // Edit Form dikhane ke liye
    public function edit($id)
    {
        $attendance = Attendance::with('employee')->findOrFail($id);
        return response()->json($attendance);
    }

    // Update karne ke liye
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'attendance' => $request->attendance, // Present or Absent
        ]);

        // Stay hours recalculate karein agar check_in/out update hue hain
        if ($attendance->check_in && $attendance->check_out) {
            $start = \Carbon\Carbon::parse($attendance->check_in);
            $end = \Carbon\Carbon::parse($attendance->check_out);
            $attendance->stay_hours = round($end->diffInMinutes($start) / 60, 2);
            $attendance->save();
        }

        return redirect()->back()->with('success', 'Record updated successfully');
    }

    // Delete karne ke liye
    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Record deleted successfully');
    }

    public function workingHoursReport(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $search = $request->input('employee_name');

        // Query ko yahan complete karein aur semi-colon lagayein
        $employees = Employee::where('is_resigned', 0)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->with([
                'attendances' => function ($q) use ($month) {
                    $q->where('attendance_date', 'like', "$month%");
                }
            ])
            ->get();

        return view('admin.attendance.working_report', compact('employees', 'month'));
    }

    public function reportPdf(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $monthYear = $request->month;

        $startDate = Carbon::parse($monthYear)->startOfMonth();
        $endDate = Carbon::parse($monthYear)->endOfMonth();

        $dates = CarbonPeriod::create($startDate, $endDate);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

        // Make sure this view exists at resources/views/attendance/report_pdf.blade.php
        return Pdf::loadView('admin.attendance.report_pdf', compact(
            'employee',
            'dates',
            'attendances',
            'monthYear'
        ))
            ->download('Attendance_Report_' . $employee->employee_id . '_' . $monthYear . '.pdf');
    }
}