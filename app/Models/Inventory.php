<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'barcode',
        'name',
        'brand',
        'model',
        'category',
        'sub_category',
        'sub_sub_category',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'condition_status',
        'quantity',
        'reuse_quantity',
        'estimated_value',
        'weight',
        'pallet_number',
        'intake_type',
        'photos_paths', 
        'notes',
        'status',
        'sync_status',
        'synced_at',
        'technician',
        'customer_phone',
        'customer_email',
        'device_type',
        'brand_category',
        'price',
        'lot_info',
        'serial_number',
        'pallet_info',
        'device_specs',
        'operating_system',
        'put_away_location',
        'repair_photos', 
    ];

    protected $casts = [
        'photos_paths' => 'array',       
        'repair_photos' => 'array',      
        'estimated_value' => 'decimal:2', 
        'price' => 'decimal:2',         
        'quantity' => 'integer',          
    ];

    public function getAvailableQuantityAttribute()
    {
        return max(0, (int) ($this->quantity ?? 0) - (int) ($this->reuse_quantity ?? 0));
    }

    /**
     * Relationship with Pallet
     */
    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet_number', 'barcode_number');
    }

    /**
     * Category Relationships
     */
    public function categoryRecord()
    {
        return $this->belongsTo(\App\Store\Models\Category::class, 'category_id');
    }

    public function subCategoryRecord()
    {
        return $this->belongsTo(\App\Store\Models\Category::class, 'sub_category_id');
    }

    public function subSubCategoryRecord()
    {
        return $this->belongsTo(\App\Store\Models\Category::class, 'sub_sub_category_id');
    }

    public function shippingRates()
    {
        return $this->hasMany(\App\Models\InventoryShippingRate::class, 'inventory_id');
    }
}
