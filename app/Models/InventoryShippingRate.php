<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'refurbishing_item_id',
        'provider',
        'servicelevel',
        'amount',
        'currency',
        'estimated_days',
        'duration_terms',
        'parcel_length',
        'parcel_width',
        'parcel_height',
        'parcel_weight',
        'distance_unit',
        'mass_unit',
        'to_street1',
        'to_city',
        'to_state',
        'to_zip',
        'to_country',
        'shippo_rate_payload',
    ];

    protected $casts = [
        'shippo_rate_payload' => 'array',
    ];
}

