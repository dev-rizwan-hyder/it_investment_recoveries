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

    /**
     * Get array of parsed individual items for 75+ items breakdown.
     */
    public function getParsedItemsAttribute(): array
    {
        $names = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->name ?? '')), fn($val) => $val !== ''));
        $brands = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->brand ?? '')), fn($val) => $val !== ''));
        $models = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->model ?? '')), fn($val) => $val !== ''));
        $serials = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->serial_number ?? '')), fn($val) => $val !== ''));

        $maxCount = max(count($names), count($brands), count($models), count($serials), 1);
        
        $items = [];
        for ($i = 0; $i < $maxCount; $i++) {
            $nameVal = $names[$i] ?? ($names[0] ?? $this->name ?? 'IT Asset Item');
            $brandVal = $brands[$i] ?? ($brands[0] ?? $this->brand ?? 'N/A');
            $modelVal = $models[$i] ?? ($models[0] ?? $this->model ?? 'N/A');
            $serialVal = $serials[$i] ?? ($serials[0] ?? '');
            
            $items[] = [
                'index' => $i + 1,
                'name' => $nameVal ?: 'IT Asset Item',
                'brand' => $brandVal ?: 'N/A',
                'model' => $modelVal ?: 'N/A',
                'serial_number' => $serialVal,
            ];
        }

        return $items;
    }

    /**
     * Get primary single item name for compact table displays.
     */
    public function getPrimaryNameAttribute(): string
    {
        $names = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->name ?? '')), fn($val) => $val !== ''));
        return $names[0] ?? ($this->name ?: 'IT Asset Item');
    }

    /**
     * Get primary single brand for compact table displays.
     */
    public function getPrimaryBrandAttribute(): string
    {
        $brands = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->brand ?? '')), fn($val) => $val !== ''));
        return $brands[0] ?? ($this->brand ?: 'N/A');
    }

    /**
     * Get primary single model for compact table displays.
     */
    public function getPrimaryModelAttribute(): string
    {
        $models = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->model ?? '')), fn($val) => $val !== ''));
        return $models[0] ?? ($this->model ?: 'N/A');
    }

    /**
     * Get primary single serial for compact table displays.
     */
    public function getPrimarySerialAttribute(): string
    {
        $serials = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->serial_number ?? '')), fn($val) => $val !== ''));
        return $serials[0] ?? ($this->serial_number ?: '');
    }

    /**
     * Get total count of distinct parsed item models.
     */
    public function getTotalParsedCountAttribute(): int
    {
        $names = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->name ?? '')), fn($val) => $val !== ''));
        $brands = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->brand ?? '')), fn($val) => $val !== ''));
        $models = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->model ?? '')), fn($val) => $val !== ''));
        $serials = array_values(array_filter(array_map('trim', preg_split('/[\n\r,]+/', $this->serial_number ?? '')), fn($val) => $val !== ''));

        return max(count($names), count($brands), count($models), count($serials), 1);
    }
}
