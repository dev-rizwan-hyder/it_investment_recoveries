<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Inhein hum direct request data se fill kar sakte hain.
     */
    protected $fillable = [
        'date',
        'category',
        'description',
        'amount',
        'payment_method',
        'vendor',
        'notes',
        'receipt_url',
    ];

    /**
     * The attributes that should be cast.
     * Date aur Amount ko sahi format mein convert karne ke liye.
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}