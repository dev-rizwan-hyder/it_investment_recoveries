<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use Illuminate\Support\Facades\Storage;

class PolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $search = $request->query('search');
        $query = Policy::query();

        if ($type) {
            $typeName = match ($type) {
                'company', 'company_policy' => 'Company Policy',
                'procedure', 'company_procedure', 'procedures' => 'Company Procedure',
                'policy_procedure', 'company_policy_procedure', 'refurbishing' => 'Company Policy & Procedure',
                default => null,
            };
            if ($typeName) {
                $query->where('type', $typeName);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('policy_id', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%')
                  ->orWhere('purpose', 'like', '%' . $search . '%')
                  ->orWhere('scope', 'like', '%' . $search . '%');
            });
        }

        $policies = $query->latest()->paginate(10)->withQueryString();
        return view('admin.policies.index', compact('policies', 'type', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Auto generate sequential Policy ID like 01, 02...
        $lastPolicy = Policy::orderByRaw('CAST(policy_id AS UNSIGNED) DESC')->first();
        $nextNum = $lastPolicy ? ((int)$lastPolicy->policy_id + 1) : 1;
        $nextPolicyId = str_pad($nextNum, 2, '0', STR_PAD_LEFT);

        $departments = \App\Models\Department::orderBy('department', 'asc')->get();

        return view('admin.policies.create', compact('nextPolicyId', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'policy_id' => 'required|string|max:255|unique:policies,policy_id',
            'type' => 'required|string|in:Company Policy,Company Procedure,Company Policy & Procedure',
            'name' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'scope' => 'nullable|string',
            'safety' => 'nullable|string',
            'visual_inspection' => 'nullable|string',
            'functional_testing' => 'nullable|string',
            'final_cleaning' => 'nullable|string',
            'cosmetic_grading' => 'nullable|string',
            'inventory_update' => 'nullable|string',
            'objectives' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'receiving_procedures' => 'nullable|string',
            'staging_procedures' => 'nullable|string',
            'violations' => 'nullable|string',
            'employee_acknowledgment' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'manager_signature' => 'nullable|string|max:255',
            'sections' => 'nullable|array',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.type' => 'nullable|string|in:paragraph,bullet',
            'sections.*.content' => 'nullable|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('policies', 'public');
        }

        $data = $request->all();
        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        Policy::create($data);

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Policy record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Policy $policy)
    {
        return view('admin.policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Policy $policy)
    {
        $departments = \App\Models\Department::orderBy('department', 'asc')->get();
        return view('admin.policies.edit', compact('policy', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'policy_id' => 'required|string|max:255|unique:policies,policy_id,' . $policy->id,
            'type' => 'required|string|in:Company Policy,Company Procedure,Company Policy & Procedure',
            'name' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'scope' => 'nullable|string',
            'safety' => 'nullable|string',
            'visual_inspection' => 'nullable|string',
            'functional_testing' => 'nullable|string',
            'final_cleaning' => 'nullable|string',
            'cosmetic_grading' => 'nullable|string',
            'inventory_update' => 'nullable|string',
            'objectives' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'receiving_procedures' => 'nullable|string',
            'staging_procedures' => 'nullable|string',
            'violations' => 'nullable|string',
            'employee_acknowledgment' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'manager_signature' => 'nullable|string|max:255',
            'sections' => 'nullable|array',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.type' => 'nullable|string|in:paragraph,bullet',
            'sections.*.content' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {

            // delete old image
            if ($policy->image && Storage::disk('public')->exists($policy->image)) {
                Storage::disk('public')->delete($policy->image);
            }

            $policy->image = $request->file('image')->store('policies', 'public');
        }

        $data = $request->all();
        $data['image'] = $policy->image;

        $policy->update($data);

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Policy record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Policy $policy)
    {
        $policy->delete();

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Policy record deleted successfully.');
    }
}
