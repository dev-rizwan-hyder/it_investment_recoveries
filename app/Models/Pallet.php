<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode_number',
        'client_id',
        'status',
        'put_away_location',
        'marketing_reference',
        'description',
        'estimated_count',
        'reuse_quantity',
        'gross_weight',
        'tare_weight',
        'reuse_weight',
        'net_weight',
        'notes',
        'receiving_employee',
        'processing_employee',
        'intake_employee',
        'ecommerce_employee',
        'refurbishing_employee',
        'universal_waste_employee',
        'data_destruction_employee',
        'photos_paths'
    ];

    /**
     * Add this casts property to handle the Array to String error
     */
    protected $casts = [
        'photos_paths' => 'array', // Yeh array ko JSON mein convert karega automatically
    ];

    /**
     * Boot the model - ensure clean data
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Ensure photos_paths is always a clean array without false/null values
            if (is_array($model->photos_paths)) {
                $model->photos_paths = array_filter($model->photos_paths, function($path) {
                    return !empty($path) && $path !== false && is_string($path);
                });
                // Re-index array to avoid JSON objects
                $model->photos_paths = array_values($model->photos_paths);
            } elseif (!is_array($model->photos_paths)) {
                $model->photos_paths = [];
            }
        });
    }

    /**
     * Get the client that owns the pallet.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}