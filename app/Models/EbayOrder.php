<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbayOrder extends Model
{
    use HasFactory;

    protected $table = 'ebay_orders';

    protected $fillable = [
        'ebay_order_id',
        'order_fulfillment_status',
        'order_payment_status',
        'cancel_state',
        'total_amount',
        'currency',
        'buyer_username',
        'ebay_created_at',
        'ebay_modified_at',
        'inventory_deducted',
        'inventory_deducted_at',
        'inventory_restored',
        'inventory_restored_at',
        'local_order_id',
        'sync_status',
        'sync_notes',
        'raw_data',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'inventory_deducted' => 'boolean',
        'inventory_restored' => 'boolean',
        'ebay_created_at' => 'datetime',
        'ebay_modified_at' => 'datetime',
        'inventory_deducted_at' => 'datetime',
        'inventory_restored_at' => 'datetime',
        'raw_data' => 'array',
    ];

    public function lineItems()
    {
        return $this->hasMany(EbayOrderLineItem::class, 'ebay_order_id', 'ebay_order_id');
    }

    public function localOrder()
    {
        return $this->belongsTo(Order::class, 'local_order_id');
    }
}
