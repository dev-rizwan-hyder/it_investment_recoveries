<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'policy_id',
        'type',
        'name',
        'reason',
        'remarks',
        'image',
        'category',
        'department',
        'effective_date',
        'purpose',
        'scope',
        'safety',
        'visual_inspection',
        'functional_testing',
        'final_cleaning',
        'cosmetic_grading',
        'inventory_update',
        'objectives',
        'responsibilities',
        'receiving_procedures',
        'staging_procedures',
        'violations',
        'employee_acknowledgment',
        'manager_name',
        'manager_signature',
        'sections',
    ];

    protected $casts = [
        'sections' => 'array',
    ];
}
