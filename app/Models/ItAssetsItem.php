<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItAssetsItem extends Model
{
    use HasFactory;

    protected $table = 'it_assets_items';

    protected $fillable = [
        'intake_type',
        'barcode',
        'name',
        'brand',
        'model',
        'serial_number',
        'category',
        'sub_category',
        'sub_sub_category',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'condition',
        'estimated_value',
        'weight',
        'reuse_weight',
        'scrap_weight',
        'quantity',
        'reuse_quantity',
        'scrap_quantity',
        'transferred_quantity',
        'notes',
        'receiving_employee',
        'processing_employee',
        'intake_employee',
        'ecommerce_employee',
        'refurbishing_employee',
        'universal_waste_employee',
        'data_destruction_employee',
        'it_assets_employee',
        'pallet_number',
        'item_placement',
        'photos_paths',
        'status',
    ];

    protected $casts = [
        'photos_paths' => 'array',
        'estimated_value' => 'float',
        'weight' => 'float',
        'transferred_quantity' => 'integer',
    ];

    public function isInventoryAdded()
    {
        return $this->status === 'Completed';
    }
}
