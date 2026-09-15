<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    public const DENVER_TIMEZONE = 'America/Denver';

    public const STATUS_IN_STORE_RESERVED = 'in_store_reserved';
    public const STATUS_IN_STORE_PURCHASED = 'in_store_purchased';
    public const STATUS_ONLINE_PURCHASED = 'online_purchased';
    public const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_READY_FOR_SHIPMENT = 'ready_for_shipment';
    public const STATUS_COMPLETED = 'completed';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';
    public const PAYMENT_STATUS_CANCELLED = 'cancelled';

    public const STATUS_CHOICES = [
        self::STATUS_IN_STORE_RESERVED => 'In store reserved',
        self::STATUS_IN_STORE_PURCHASED => 'In store Purchased',
        self::STATUS_ONLINE_PURCHASED => 'Online Purchased',
        self::STATUS_READY_FOR_PICKUP => 'Ready for pickup',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_READY_FOR_SHIPMENT => 'Ready for shipment',
        self::STATUS_COMPLETED => 'Completed',
    ];

    public const STATUS_BADGE_CLASSES = [
        self::STATUS_IN_STORE_RESERVED => 'bg-amber-100 text-amber-700',
        self::STATUS_IN_STORE_PURCHASED => 'bg-sky-100 text-sky-700',
        self::STATUS_ONLINE_PURCHASED => 'bg-emerald-100 text-emerald-700',
        self::STATUS_READY_FOR_PICKUP => 'bg-blue-100 text-blue-700',
        self::STATUS_SHIPPED => 'bg-indigo-100 text-indigo-700',
        self::STATUS_CANCELLED => 'bg-red-100 text-red-700',
        self::STATUS_READY_FOR_SHIPMENT => 'bg-violet-100 text-violet-700',
        self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-700',
    ];

    public const STATUS_SELECT_CLASSES = [
        self::STATUS_IN_STORE_RESERVED => 'bg-yellow-50 text-yellow-800 border-yellow-200 focus:ring-yellow-200',
        self::STATUS_IN_STORE_PURCHASED => 'bg-sky-50 text-sky-800 border-sky-200 focus:ring-sky-200',
        self::STATUS_ONLINE_PURCHASED => 'bg-emerald-50 text-emerald-800 border-emerald-200 focus:ring-emerald-200',
        self::STATUS_READY_FOR_PICKUP => 'bg-amber-50 text-amber-800 border-amber-200 focus:ring-amber-200',
        self::STATUS_SHIPPED => 'bg-purple-50 text-purple-800 border-purple-200 focus:ring-purple-200',
        self::STATUS_CANCELLED => 'bg-red-50 text-red-800 border-red-200 focus:ring-red-200',
        self::STATUS_READY_FOR_SHIPMENT => 'bg-blue-50 text-blue-800 border-blue-200 focus:ring-blue-200',
        self::STATUS_COMPLETED => 'bg-teal-50 text-teal-800 border-teal-200 focus:ring-teal-200',
    ];

    public const PAYMENT_STATUS_CHOICES = [
        self::PAYMENT_STATUS_PENDING => 'Pending',
        self::PAYMENT_STATUS_PAID => 'Paid',
        self::PAYMENT_STATUS_FAILED => 'Failed',
        self::PAYMENT_STATUS_REFUNDED => 'Refunded',
        self::PAYMENT_STATUS_CANCELLED => 'Cancelled',
    ];

    public const PAYMENT_STATUS_BADGE_CLASSES = [
        self::PAYMENT_STATUS_PENDING => 'bg-amber-100 text-amber-700',
        self::PAYMENT_STATUS_PAID => 'bg-emerald-100 text-emerald-700',
        self::PAYMENT_STATUS_FAILED => 'bg-red-100 text-red-700',
        self::PAYMENT_STATUS_REFUNDED => 'bg-sky-100 text-sky-700',
        self::PAYMENT_STATUS_CANCELLED => 'bg-slate-200 text-slate-700',
    ];

    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'shipping',
        'total',
        'status',
        'payment_method',
        'payment_id',
        'payment_status',
        'fulfillment_type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'pickup_contact_name',
        'pickup_contact_phone',
        'pickup_location',
        'pickup_slot',
        'shipping_selection_payload',
        'shippo_shipment_id',
        'shippo_rate_id',
        'shippo_transaction_id',
        'tracking_number',
        'carrier',
        'label_url',
        'label_image_url',
        'shipping_status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_selection_payload' => 'array',
    ];

    public static function statusChoices(): array
    {
        return self::STATUS_CHOICES;
    }

    public static function statusValues(): array
    {
        return array_keys(self::STATUS_CHOICES);
    }

    public static function statusLabel(?string $status): string
    {
        return self::STATUS_CHOICES[$status] ?? ucwords(str_replace('_', ' ', (string) $status));
    }

    public static function statusBadgeClass(?string $status): string
    {
        return self::STATUS_BADGE_CLASSES[$status] ?? 'bg-slate-100 text-slate-700';
    }

    public static function statusSelectClass(?string $status): string
    {
        return self::STATUS_SELECT_CLASSES[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200 focus:ring-slate-200';
    }

    public static function paymentStatusChoices(): array
    {
        return self::PAYMENT_STATUS_CHOICES;
    }

    public static function paymentStatusValues(): array
    {
        return array_keys(self::PAYMENT_STATUS_CHOICES);
    }

    public static function paymentStatusLabel(?string $status): string
    {
        if (empty($status)) {
            return self::PAYMENT_STATUS_CHOICES[self::PAYMENT_STATUS_PENDING];
        }

        return self::PAYMENT_STATUS_CHOICES[$status] ?? ucwords(str_replace('_', ' ', (string) $status));
    }

    public static function paymentStatusBadgeClass(?string $status): string
    {
        if (empty($status)) {
            return self::PAYMENT_STATUS_BADGE_CLASSES[self::PAYMENT_STATUS_PENDING];
        }

        return self::PAYMENT_STATUS_BADGE_CLASSES[$status] ?? 'bg-slate-100 text-slate-700';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusLabel($this->payment_status);
    }

    public function placedAtDenver(): ?Carbon
    {
        return $this->created_at?->copy()->timezone(self::DENVER_TIMEZONE);
    }

    public function formatPlacedAtDenver(string $format = 'M d, Y h:i A T', string $fallback = 'N/A'): string
    {
        return $this->placedAtDenver()?->format($format) ?? $fallback;
    }

    public function getTaxAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        $tax = (float) $this->total - (float) $this->subtotal - (float) $this->shipping;

        return round(max(0, $tax), 2);
    }

    /**
     * Get the user who owns the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }
}
