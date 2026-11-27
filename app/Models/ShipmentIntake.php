<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentIntake extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shipment_intakes';

    protected $fillable = [
        'intake_date',
        'client_id',
        'job_no',
        'barcode_number',
        'universal_waste_item_id',
        'pallet_number',
        'description',
        'estimated_count',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'put_away_location',
        'marketing_reference',
        'receiving_employee',
        'notes',
        'photos_paths',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'photos_paths' => 'array',
        'intake_date'  => 'date',
        'gross_weight' => 'float',
        'tare_weight'  => 'float',
        'net_weight'   => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (is_array($model->photos_paths)) {
                $model->photos_paths = array_values(array_filter($model->photos_paths, fn($p) => !empty($p) && is_string($p)));
            } else {
                $model->photos_paths = [];
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    public function universalWasteItem(): BelongsTo
    {
        return $this->belongsTo(UniversalWasteItem::class, 'universal_waste_item_id');
    }
}
