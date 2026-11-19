<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = Department::query();

        if ($search !== '') {
            $query->where('department', 'like', '%' . $search . '%');
        }

        $departments = $query->orderBy('department', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.departments.index', compact('departments', 'search'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department' => 'required|string|max:255|unique:departments,department',
        ]);

        Department::create([
            'department' => trim($request->input('department')),
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department' => 'required|string|max:255|unique:departments,department,' . $department->id,
        ]);

        $department->update([
            'department' => trim($request->input('department')),
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        // Prevent deletion if employees are assigned to this department
        if ($department->employees()->exists()) {
            return redirect()
                ->route('admin.departments.index')
                ->with('error', 'Cannot delete department as it has assigned employees.');
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
