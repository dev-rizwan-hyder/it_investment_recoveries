<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class TimeTrackingController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->get();
        return view('admin.time-tracking', compact('locations'));
    }

    public function assignView()
    {
        $locations = Location::all();
        // Employee model use karein jo aapne dikhayi thi
        $employees = \App\Models\Employee::where('is_resigned', false)->get();

        return view('admin.assign-employee', compact('locations', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
        ]);

        Location::create($validated);

        // Ensure 'admin.time-tracking.index' matches your route name in web.php
        return redirect()->back()->with('success', 'Location added successfully!');
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
        ]);

        $location->update($validated);

        return redirect()->back()->with('success', 'Location updated successfully!');
    }

    // --- Ye function aapke Controller ke andar hona chahiye ---
    public function saveAssignment(Request $request)
    {
        // 1. Data Validate karein
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id',
        ]);

        // 2. Employee ko find karein
        $employee = \App\Models\Employee::findOrFail($request->employee_id);

        // 3. Pivot table (employee_location) mein data sync karein
        // Iske liye Employee model mein locations() function hona zaroori hai
        $employee->locations()->sync($request->location_ids ?: []);

        return redirect()->back()->with('success', 'Locations successfully assigned to ' . $employee->name . '!');
    }

    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return redirect()->route('admin.time-tracking')->with('success', 'Location deleted!');
    }
}