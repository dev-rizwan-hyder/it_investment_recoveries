<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $table = 'repair_entries';

    protected $fillable = [
        'name', 
        'category', 
        'status', 
        'price', 
        'has_specs', 
        'repair_status', 
        'sold_at'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'has_specs' => 'boolean',
    ];
}