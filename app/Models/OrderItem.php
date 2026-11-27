<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_type',
        'device_type',
        'location',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * Get the order that owns the item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the related inventory record when this order item comes from inventory.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'product_id')->withTrashed();
    }

    /**
     * Get raw photo paths associated with this order item from inventory.
     */
    public function getPhotosAttribute(): array
    {
        $photos = [];
        $inv = $this->inventory;
        if ($inv) {
            if (!empty($inv->photos_paths) && is_array($inv->photos_paths)) {
                $photos = array_merge($photos, array_values(array_filter($inv->photos_paths)));
            }
            if (!empty($inv->repair_photos) && is_array($inv->repair_photos)) {
                $photos = array_merge($photos, array_values(array_filter($inv->repair_photos)));
            }
        }

        return array_values(array_unique($photos));
    }

    /**
     * Get full browser-accessible URLs for this order item's images.
     */
    public function getPhotoUrlsAttribute(): array
    {
        return array_map([self::class, 'formatPhotoUrl'], $this->photos);
    }

    /**
     * Format a photo path into a clean, accessible URL.
     */
    public static function formatPhotoUrl(?string $path): string
    {
        if (empty($path)) {
            return asset('images/placeholder.png');
        }

        $path = trim((string) $path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        $rootFolders = ['pallets/', 'refurbishing/', 'repair/', 'repairentry/', 'images/', 'sales/'];
        foreach ($rootFolders as $folder) {
            if (str_starts_with($cleanPath, $folder)) {
                return asset($cleanPath);
            }
        }

        return asset('storage/' . $cleanPath);
    }

    /**
     * Resolve device type from stored value first, then inventory fallback.
     */
    public function getDeviceTypeAttribute($value)
    {
        $current = is_string($value) ? trim($value) : $value;
        if (!empty($current)) {
            return $current;
        }

        $inventoryDeviceType = is_string($this->inventory?->device_type) ? trim($this->inventory?->device_type) : $this->inventory?->device_type;
        if (!empty($inventoryDeviceType)) {
            return $inventoryDeviceType;
        }

        return $this->product_name ?: 'N/A';
    }

    /**
     * Resolve location from stored value first, then inventory put-away location fallback.
     */
    public function getLocationAttribute($value)
    {
        $current = is_string($value) ? trim($value) : $value;
        if (!empty($current)) {
            return $current;
        }

        $inventoryLocation = is_string($this->inventory?->put_away_location) ? trim($this->inventory?->put_away_location) : $this->inventory?->put_away_location;
        if (!empty($inventoryLocation)) {
            return $inventoryLocation;
        }

        $palletInfo = is_string($this->inventory?->pallet_info) ? trim($this->inventory?->pallet_info) : $this->inventory?->pallet_info;
        if (!empty($palletInfo)) {
            return $palletInfo;
        }

        return 'N/A';
    }

    /**
     * Resolve brand/category from stored value first, then inventory fallback.
     */
    public function getBrandCategoryAttribute($value)
    {
        $current = is_string($value) ? trim($value) : $value;
        if (!empty($current)) {
            return $current;
        }

        $inventoryBrandCategory = is_string($this->inventory?->brand_category) ? trim($this->inventory?->brand_category) : $this->inventory?->brand_category;
        if (!empty($inventoryBrandCategory)) {
            return $inventoryBrandCategory;
        }

        return $this->product_name ?: 'N/A';
    }

    public function getDeviceSpecsAttribute($value)
    {
        $current = is_string($value) ? trim($value) : $value;
        if (!empty($current)) {
            return $current;
        }

        $inventoryDeviceSpecification = is_string($this->inventory?->device_specs) ? trim($this->inventory?->device_specs) : $this->inventory?->device_specs;
        if (!empty($inventoryDeviceSpecification)) {
            return $inventoryDeviceSpecification;
        }

        return $this->product_name ?: 'N/A';
    }

    public function getOperatingSystemAttribute($value)
    {
        $current = is_string($value) ? trim($value) : $value;
        if (!empty($current)) {
            return $current;
        }

        $inventoryOperatingSystem = is_string($this->inventory?->operating_system) ? trim($this->inventory?->operating_system) : $this->inventory?->operating_system;
        if (!empty($inventoryOperatingSystem)) {
            return $inventoryOperatingSystem;
        }

        return $this->product_name ?: 'N/A';
    }
}
