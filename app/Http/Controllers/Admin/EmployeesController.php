<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Policy;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EmployeesController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_resigned', 0);
            } elseif ($status === 'offboarded') {
                $query->where('is_resigned', 1);
            }
        }

        // Policy filter
        if ($request->filled('policy_id')) {
            $policyId = $request->input('policy_id');
            $query->where('application_data->selected_policy_id', $policyId);
        }

        $employees = $query->orderBy('sort_order', 'asc')->paginate(15)->withQueryString();
        $policies = Policy::orderBy('department', 'asc')->get();

        return view('admin.employees.index', compact('employees', 'policies'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $policies = Policy::orderBy('department', 'asc')->get();
        $numericIds = Employee::pluck('employee_id')
            ->map(function($id) {
                if (preg_match('/(?:emp-)?0*(\d+)/i', $id, $matches)) {
                    return (int)$matches[1];
                }
                return is_numeric($id) ? (int)$id : 0;
            });
        $nextNum = $numericIds->max() + 1;
        if ($nextNum < 1) {
            $nextNum = 1;
        }
        $nextEmployeeId = 'EMP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        return view('admin.employees.create', compact('policies', 'nextEmployeeId'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'cnic' => 'required|unique:employees,cnic',
            'mobile_number' => 'nullable|string|max:50',
            'employee_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'policy_id' => 'required|exists:policies,id',
            'application' => 'required|array',
            'application.policy_signature' => 'required|string',
            'application.applicant_signature' => 'required|string',
        ]);

        $data = $request->only([
            'employee_id',
            'name',
            'father_name',
            'email',
            'cnic',
            'mobile_number',
            'dob',
            'employee_department_id',
            'designation_id',
            'joining_date',
            'project_name',
            'deployment_area',
            'deployment_site',
            'total_leaves',
            'used_leaves',
            'remaining_leaves',
            'comment',
        ]);

        if ($request->hasFile('employee_picture')) {
            $file = $request->file('employee_picture');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('employee-profile'), $filename);
            $data['employee_picture'] = $filename;
        }

        $policy = Policy::findOrFail($request->policy_id);
        $applicationData = $request->input('application', []);

        $policySignaturePath = $this->saveBase64Signature(
            $applicationData['policy_signature'] ?? null,
            'employee-signatures',
            'policy-' . $request->employee_id
        );

        $applicantSignaturePath = $this->saveBase64Signature(
            $applicationData['applicant_signature'] ?? null,
            'employee-signatures',
            'applicant-' . $request->employee_id
        );

        if (!$policySignaturePath || !$applicantSignaturePath) {
            throw ValidationException::withMessages([
                'signature' => 'Policy signature and applicant signature are required.',
            ]);
        }

        $applicationData['selected_policy_id'] = $policy->id;
        $applicationData['selected_policy_title'] = $policy->title ?? $policy->name ?? null;
        $applicationData['selected_policy_department'] = $policy->department ?? $policy->department_name ?? null;
        $applicationData['policy_signature_path'] = $policySignaturePath;
        $applicationData['applicant_signature_path'] = $applicantSignaturePath;
        $applicationData['signed_at'] = now()->toDateTimeString();

        unset($applicationData['policy_signature']);
        unset($applicationData['applicant_signature']);

        $data['application_data'] = $applicationData;
        $data['is_resigned'] = $request->has('is_resigned') ? 1 : 0;

        Employee::create($data);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    private function saveBase64Signature(?string $base64, string $folder, string $prefix): ?string
    {
        if (!$base64 || !preg_match('/^data:image\/png;base64,/', $base64)) {
            return null;
        }

        $image = base64_decode(substr($base64, strpos($base64, ',') + 1));

        if (!$image) {
            return null;
        }

        $filename = Str::slug($prefix) . '-' . time() . '.png';
        $path = $folder . '/' . $filename;

        Storage::disk('public')->put($path, $image);

        // Also save to public/employee-signature
        $publicDir = public_path('employee-signature');
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        file_put_contents($publicDir . '/' . $filename, $image);

        return $path;
    }
    public function show(Employee $employee)
    {
        $policy = null;
        $policyId = data_get($employee->application_data, 'selected_policy_id');
        if ($policyId) {
            $policy = \App\Models\Policy::find($policyId);
        }
        return view('admin.employees.show', compact('employee', 'policy'));
    }

    /**
     * Display the employee profile report.
     */
    public function profileReport(Employee $employee, Request $request)
    {
        // Get active tab from request (attendance, absents, or default to personal)
        $activeTab = $request->input('tab', 'personal');

        // Get date range from request or set defaults (3 days ago to today)
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : Carbon::now();
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : Carbon::now()->subDays(3);

        // Fetch present records within date range
        $attendances = Attendance::where('employee_id', $employee->id)
            ->where('attendance', 'Present')
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Fetch absent records within date range
        $absents = Attendance::where('employee_id', $employee->id)
            ->where('attendance', 'Absent')
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('admin.employees.profile-report', compact('employee', 'attendances', 'absents', 'fromDate', 'toDate', 'activeTab'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $policies = Policy::orderBy('department', 'asc')->get();
        $appData = $employee->application_data ?? [];
        $availability = $appData['availability'] ?? [];
        return view('admin.employees.edit', compact('employee', 'policies', 'appData', 'availability'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_id'      => 'required|unique:employees,employee_id,' . $employee->id,
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:employees,email,' . $employee->id,
            'cnic'             => 'required|unique:employees,cnic,' . $employee->id,
            'employee_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'policy_id'        => 'nullable|exists:policies,id',
        ]);

        $data = $request->only([
            'employee_id',
            'name',
            'father_name',
            'email',
            'cnic',
            'mobile_number',
            'dob',
            'employee_department_id',
            'designation_id',
            'joining_date',
            'project_name',
            'deployment_area',
            'deployment_site',
            'total_leaves',
            'used_leaves',
            'remaining_leaves',
            'comment',
        ]);

        if ($request->hasFile('employee_picture')) {
            if ($employee->employee_picture) {
                $oldPath = public_path('employee-profile/' . $employee->employee_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $file = $request->file('employee_picture');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('employee-profile'), $filename);
            $data['employee_picture'] = $filename;
        }

        $data['is_resigned'] = $request->has('is_resigned') ? 1 : 0;

        $applicationData = $request->input('application', []);
        $existingAppData = $employee->application_data ?? [];

        if ($request->filled('policy_id')) {
            $policy = Policy::find($request->policy_id);
            if ($policy) {
                $applicationData['selected_policy_id'] = $policy->id;
                $applicationData['selected_policy_title'] = $policy->title ?? $policy->name ?? null;
                $applicationData['selected_policy_department'] = $policy->department ?? $policy->department_name ?? null;
            }
        } else {
            $applicationData['selected_policy_id'] = $existingAppData['selected_policy_id'] ?? null;
            $applicationData['selected_policy_title'] = $existingAppData['selected_policy_title'] ?? null;
            $applicationData['selected_policy_department'] = $existingAppData['selected_policy_department'] ?? null;
        }

        $policySignature = $applicationData['policy_signature'] ?? null;
        if ($policySignature && preg_match('/^data:image\/png;base64,/', $policySignature)) {
            if (!empty($existingAppData['policy_signature_path'])) {
                Storage::disk('public')->delete($existingAppData['policy_signature_path']);
                $oldPublicFile = public_path('employee-signature/' . basename($existingAppData['policy_signature_path']));
                if (file_exists($oldPublicFile)) {
                    unlink($oldPublicFile);
                }
            }
            $policySignaturePath = $this->saveBase64Signature(
                $policySignature,
                'employee-signatures',
                'policy-' . $request->employee_id
            );
            $applicationData['policy_signature_path'] = $policySignaturePath;
        } else {
            $applicationData['policy_signature_path'] = $existingAppData['policy_signature_path'] ?? null;
        }

        $applicantSignature = $applicationData['applicant_signature'] ?? null;
        if ($applicantSignature && preg_match('/^data:image\/png;base64,/', $applicantSignature)) {
            if (!empty($existingAppData['applicant_signature_path'])) {
                Storage::disk('public')->delete($existingAppData['applicant_signature_path']);
                $oldPublicFile = public_path('employee-signature/' . basename($existingAppData['applicant_signature_path']));
                if (file_exists($oldPublicFile)) {
                    unlink($oldPublicFile);
                }
            }
            $applicantSignaturePath = $this->saveBase64Signature(
                $applicantSignature,
                'employee-signatures',
                'applicant-' . $request->employee_id
            );
            $applicationData['applicant_signature_path'] = $applicantSignaturePath;
        } else {
            $applicationData['applicant_signature_path'] = $existingAppData['applicant_signature_path'] ?? null;
        }

        $applicationData['signed_at'] = $existingAppData['signed_at'] ?? now()->toDateTimeString();

        unset($applicationData['policy_signature']);
        unset($applicationData['applicant_signature']);

        $data['application_data'] = $applicationData;

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }
}
