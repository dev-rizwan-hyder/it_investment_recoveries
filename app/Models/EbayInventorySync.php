<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EbayInventorySync extends Model
{
    use HasFactory;

    protected $table = 'ebay_inventory_syncs';

    protected $fillable = [
        'inventory_id',
        'order_id',
        'sku',
        'ebay_item_id',
        'ebay_offer_id',
        'synced_quantity',
        'status',
        'sync_mode',
        'attempts',
        'last_attempt_at',
        'last_success_at',
        'error_message',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'synced_quantity' => 'integer',
        'attempts' => 'integer',
        'last_attempt_at' => 'datetime',
        'last_success_at' => 'datetime',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
