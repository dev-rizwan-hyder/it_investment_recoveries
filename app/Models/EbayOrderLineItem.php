<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbayOrderLineItem extends Model
{
    use HasFactory;

    protected $table = 'ebay_order_line_items';

    protected $fillable = [
        'ebay_order_id',
        'line_item_id',
        'legacy_item_id',
        'sku',
        'title',
        'quantity_purchased',
        'quantity_deducted',
        'quantity_restored',
        'unit_price',
        'total_price',
        'inventory_id',
        'status',
    ];

    protected $casts = [
        'quantity_purchased' => 'integer',
        'quantity_deducted' => 'integer',
        'quantity_restored' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(EbayOrder::class, 'ebay_order_id', 'ebay_order_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
