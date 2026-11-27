<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const DENVER_TIMEZONE = 'America/Denver';

    public const STATUS_IN_STORE_RESERVED = 'in_store_reserved';
    public const STATUS_IN_STORE_PURCHASED = 'in_store_purchased';
    public const STATUS_ONLINE_PURCHASED = 'online_purchased';
    public const STATUS_IN_STORE_PAYMENT = 'in_store_payment';
    public const STATUS_ONLINE_PAYMENT = 'online_payment';
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
        self::STATUS_IN_STORE_PURCHASED => 'In store payment',
        self::STATUS_ONLINE_PURCHASED => 'Online payment',
        self::STATUS_READY_FOR_PICKUP => 'Ready for pickup',
        self::STATUS_SHIPPED => 'Shipped & Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_READY_FOR_SHIPMENT => 'Ready for shipment',
    ];

    public const STATUS_BADGE_CLASSES = [
        self::STATUS_IN_STORE_RESERVED => 'bg-amber-100 text-amber-700',
        self::STATUS_IN_STORE_PURCHASED => 'bg-sky-100 text-sky-700',
        self::STATUS_ONLINE_PURCHASED => 'bg-emerald-100 text-emerald-700',
        self::STATUS_IN_STORE_PAYMENT => 'bg-sky-100 text-sky-700',
        self::STATUS_ONLINE_PAYMENT => 'bg-emerald-100 text-emerald-700',
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
        self::STATUS_IN_STORE_PAYMENT => 'bg-sky-50 text-sky-800 border-sky-200 focus:ring-sky-200',
        self::STATUS_ONLINE_PAYMENT => 'bg-emerald-50 text-emerald-800 border-emerald-200 focus:ring-emerald-200',
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
        'discount',
        'discount_code',
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
        'discount' => 'float',
    ];

    public static function statusChoices(): array
    {
        return self::STATUS_CHOICES;
    }

    public static function statusValues(): array
    {
        return array_unique(array_merge(array_keys(self::STATUS_CHOICES), [self::STATUS_COMPLETED]));
    }

    public static function statusLabel(?string $status): string
    {
        if ($status === self::STATUS_COMPLETED) {
            return 'Shipped & Completed';
        }

        return self::STATUS_CHOICES[$status] ?? ucwords(str_replace('_', ' ', (string) $status));
    }

    public static function statusBadgeClass(?string $status): string
    {
        if ($status === self::STATUS_COMPLETED) {
            return self::STATUS_BADGE_CLASSES[self::STATUS_SHIPPED] ?? 'bg-indigo-100 text-indigo-700';
        }

        return self::STATUS_BADGE_CLASSES[$status] ?? 'bg-slate-100 text-slate-700';
    }

    public static function statusSelectClass(?string $status): string
    {
        if ($status === self::STATUS_COMPLETED) {
            return self::STATUS_SELECT_CLASSES[self::STATUS_SHIPPED] ?? 'bg-purple-50 text-purple-800 border-purple-200 focus:ring-purple-200';
        }

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

        $discountedSubtotal = (float) $this->subtotal - (float) ($this->discount ?? 0);
        $tax = (float) $this->total - $discountedSubtotal - (float) $this->shipping;

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
     * Associated Sales Invoices
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'order_id');
    }

    /**
     * Get the latest sales invoice for this order
     */
    public function getSalesInvoiceAttribute(): ?SalesInvoice
    {
        if (Schema::hasColumn('sales_invoices', 'order_id')) {
            $invoice = SalesInvoice::where('order_id', $this->id)->latest('id')->first();
            if ($invoice) {
                return $invoice;
            }
        }

        return SalesInvoice::where(function ($q) {
            $q->where('message', 'like', "%Order #{$this->order_number}%");
            if (!empty($this->id)) {
                $q->orWhere('message', 'like', "%Order ID #{$this->id}%");
            }
        })->latest('id')->first();
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

    /**
     * Get all product images for this order, grouped with product information.
     */
    public function getProductImagesGrouped(): array
    {
        $groups = [];
        foreach ($this->items as $item) {
            $urls = $item->photo_urls;
            $productName = $item->product_name ?: ($item->device_type ?: 'Product');
            $groups[] = [
                'item_id' => $item->id,
                'product_name' => $productName,
                'device_type' => $item->device_type,
                'quantity' => (int) ($item->quantity ?? 1),
                'unit_price' => (float) ($item->unit_price ?? 0),
                'total_price' => (float) ($item->total_price ?? 0),
                'photos' => $urls,
                'first_photo' => $urls[0] ?? asset('images/placeholder.png'),
                'has_real_photos' => !empty($urls),
            ];
        }

        return $groups;
    }

    /**
     * Get a flat list of all product photo URLs across all items in this order.
     */
    public function getAllProductPhotoUrls(): array
    {
        $urls = [];
        foreach ($this->items as $item) {
            foreach ($item->photo_urls as $url) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * Check if this order has a shipping label available.
     */
    public function hasShippingLabel(): bool
    {
        return !empty($this->label_url) || !empty($this->label_image_url) || !empty($this->shippo_transaction_id);
    }

    /**
     * Get the resolved, browser-accessible shipping label URL.
     */
    public function getShippingLabelUrlAttribute(): ?string
    {
        $raw = $this->label_url ?: $this->label_image_url;
        if (empty($raw)) {
            return null;
        }

        $raw = trim((string) $raw);
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:')) {
            return $raw;
        }

        $clean = ltrim($raw, '/');
        if (str_starts_with($clean, 'storage/')) {
            return asset($clean);
        }

        return asset('storage/' . $clean);
    }

    /**
     * Sync shipping label details directly from Shippo by transaction ID.
     */
    public function syncShippingLabelFromShippo(): ?string
    {
        if (empty($this->shippo_transaction_id)) {
            return null;
        }

        $shippoKey = env('SHIPPO_API_KEY');
        if (!$shippoKey || str_contains($shippoKey, 'xxxxxxxx')) {
            return null;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'ShippoToken ' . $shippoKey,
            ])->acceptJson()->get("https://api.goshippo.com/transactions/{$this->shippo_transaction_id}/");

            if ($response->successful()) {
                $data = $response->json();
                $labelUrl = $data['label_url'] ?? null;
                $trackingNumber = $data['tracking_number'] ?? null;
                $carrier = is_array($data['rate'] ?? null) ? ($data['rate']['provider'] ?? null) : null;

                $updateData = [];
                if ($labelUrl) $updateData['label_url'] = $labelUrl;
                if ($trackingNumber && empty($this->tracking_number)) $updateData['tracking_number'] = $trackingNumber;
                if ($carrier && empty($this->carrier)) $updateData['carrier'] = $carrier;

                if (!empty($updateData)) {
                    $this->update($updateData);
                }

                return $labelUrl;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error syncing Shippo label for order #{$this->id}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Search Shippo API for any transaction matching this order number or rate.
     */
    public function findShippingLabelInShippo(): ?string
    {
        $shippoKey = env('SHIPPO_API_KEY');
        if (!$shippoKey || str_contains($shippoKey, 'xxxxxxxx')) {
            return null;
        }

        $rateId = $this->shippo_rate_id;
        if (!$rateId && is_array($this->shipping_selection_payload)) {
            $rateId = $this->shipping_selection_payload['id'] ?? ($this->shipping_selection_payload['rate_id'] ?? null);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'ShippoToken ' . $shippoKey,
            ])->acceptJson()->get('https://api.goshippo.com/transactions/?results=50');

            if ($response->successful()) {
                $transactions = $response->json('results', []);
                foreach ($transactions as $tx) {
                    $metadata = (string) ($tx['metadata'] ?? '');
                    $txRate = is_array($tx['rate'] ?? null) ? ($tx['rate']['object_id'] ?? '') : (string) ($tx['rate'] ?? '');

                    if (
                        (!empty($metadata) && (str_contains($metadata, $this->order_number) || str_contains($metadata, (string) $this->id))) ||
                        (!empty($rateId) && $txRate === $rateId)
                    ) {
                        $labelUrl = $tx['label_url'] ?? null;
                        if ($labelUrl) {
                            $carrier = is_array($tx['rate'] ?? null) ? ($tx['rate']['provider'] ?? null) : null;
                            $this->update([
                                'shippo_transaction_id' => $tx['object_id'] ?? null,
                                'label_url' => $labelUrl,
                                'tracking_number' => $tx['tracking_number'] ?? $this->tracking_number,
                                'carrier' => $carrier ?? $this->carrier,
                            ]);
                            return $labelUrl;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Shippo lookup failed for order #{$this->id}: " . $e->getMessage());
        }

        return null;
    }
}
