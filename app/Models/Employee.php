<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    // Allows all fields to be mass-assigned
    protected $guarded = [];

    /**
     * RELATIONSHIPS
     */

    // Designation relation (Needed for your Index/Show/Edit pages)
    public function designation()
    {
        // Ensure you have a model named 'Designation'
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    // Department relation
    public function department()
    {
        // NOTE: If your model is named 'Department' instead of 'EmployeeDepartment', 
        // change 'EmployeeDepartment::class' to 'Department::class' below.
        if (class_exists('App\Models\EmployeeDepartment')) {
            return $this->belongsTo(EmployeeDepartment::class, 'employee_department_id');
        }
        return $this->belongsTo(Department::class, 'employee_department_id');
    }

    // Project relation
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Attendance relation
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'id');
    }

    // Leaves relation
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'id_number', 'employee_id');
    }

    // Salary relation
    public function salary()
    {
        return $this->hasOne(EmployeeSalary::class, 'employee_id');
    }

    // Bank relation
    public function bank()
    {
        return $this->belongsTo(CompanyBank::class, 'company_bank_id');
    }

    // Add this inside your Employee class
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'employee_location', 'employee_id', 'location_id');
    }

    /**
     * HELPER ATTRIBUTES
     */

    // Example: $employee->status_label
    public function getStatusLabelAttribute()
    {
        return $this->is_resigned ? 'Resigned' : 'Active';
    }

    protected $casts = [
        'application_data' => 'array',
        'is_resigned' => 'boolean',
    ];
}
