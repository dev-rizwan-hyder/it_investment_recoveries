<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_CHEQUE = 'cheque';
    public const PAYMENT_METHOD_SQUARE_POS = 'square_pos';
    public const PAYMENT_METHOD_SQUARE_CARD = 'square_card';
    public const PAYMENT_METHOD_ONLINE = 'online';

    public const PAYMENT_TYPE_IN_STORE = 'in_store';
    public const PAYMENT_TYPE_ONLINE = 'online_payment';

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_CHOICES = [
        self::PAYMENT_METHOD_CASH => 'Cash',
        self::PAYMENT_METHOD_CHEQUE => 'Cheque',
        self::PAYMENT_METHOD_SQUARE_POS => 'Square POS',
        self::PAYMENT_METHOD_ONLINE => 'Online',
    ];

    public const PAYMENT_TYPE_CHOICES = [
        self::PAYMENT_TYPE_IN_STORE => 'In store payment',
        self::PAYMENT_TYPE_ONLINE => 'online payment',
    ];

    public const STATUS_CHOICES = [
        self::STATUS_UNPAID => 'Unpaid',
        self::STATUS_PAID => 'Paid',
    ];

    public const STATUS_BADGE_CLASSES = [
        self::STATUS_UNPAID => 'bg-amber-100 text-amber-700',
        self::STATUS_PAID => 'bg-emerald-100 text-emerald-600',
    ];

    protected $fillable = [
        'order_id',
        'repair_entry_id',
        'client_id',
        'created_by',
        'invoice_number',
        'invoice_title',
        'service_date',
        'due_date',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'customer_address',
        'message',
        'picture_paths',
        'payment_method',
        'payment_type',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'status',
        'paid_at',
        'square_invoice_id',
        'square_order_id',
        'square_customer_id',
        'square_public_url',
        'square_status',
        'square_sent_at',
        'square_reminded_at',
        'square_reminder_count',
        'square_last_reminder_message',
    ];

    protected $casts = [
        'service_date' => 'date',
        'due_date' => 'date',
        'picture_paths' => 'array',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'square_sent_at' => 'datetime',
        'square_reminded_at' => 'datetime',
        'square_reminder_count' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (SalesInvoice $invoice) {
            if (empty($invoice->invoice_title)) {
                $invoice->invoice_title = $invoice->invoice_number ?: 'Invoice';
            }
        });

        static::saved(function (SalesInvoice $invoice) {
            $invoice->syncLinkedRepairEntry();
            if ($invoice->relationLoaded('items') || $invoice->items()->exists()) {
                $invoice->syncInventoryStockAndEbay();
            }
        });

        static::deleting(function (SalesInvoice $invoice) {
            $invoice->restoreDeductedInventoryStock();
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $lastInvoiceNumber = self::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $nextNumber = $lastInvoiceNumber
            ? ((int) Str::afterLast($lastInvoiceNumber, '-') + 1)
            : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function paymentMethodValues(): array
    {
        return [
            self::PAYMENT_METHOD_CASH,
            self::PAYMENT_METHOD_CHEQUE,
            self::PAYMENT_METHOD_SQUARE_POS,
            self::PAYMENT_METHOD_SQUARE_CARD,
            self::PAYMENT_METHOD_ONLINE,
        ];
    }

    public static function statusChoices(): array
    {
        return self::STATUS_CHOICES;
    }

    public static function statusValues(): array
    {
        return [self::STATUS_UNPAID, self::STATUS_PAID, self::STATUS_DRAFT, self::STATUS_SENT, self::STATUS_CANCELLED];
    }

    public static function paymentMethodLabel(?string $method): string
    {
        if ($method === 'square_card' || $method === 'square_pos') {
            return 'Square POS';
        }

        return self::PAYMENT_METHOD_CHOICES[$method] ?? ucwords(str_replace('_', ' ', (string) $method));
    }

    public static function statusLabel(?string $status): string
    {
        return strtolower((string) $status) === self::STATUS_PAID ? 'Paid' : 'Unpaid';
    }

    public static function statusBadgeClass(?string $status): string
    {
        return strtolower((string) $status) === self::STATUS_PAID
            ? 'bg-emerald-100 text-emerald-600'
            : 'bg-amber-100 text-amber-700';
    }

    public static function pictureUrl(?string $path): string
    {
        $path = (string) $path;

        if (Str::startsWith($path, 'data:')) {
            // If it's an image data URI, strip any ;name=...; parameter before passing to img src
            if (Str::startsWith($path, 'data:image/') && preg_match('/^data:image\/[^;]+;name=[^;]+;base64,/i', $path)) {
                return preg_replace('/^data:(image\/[^;]+);name=[^;]+;base64,/i', 'data:$1;base64,', $path);
            }
            return $path;
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['sales/', 'storage/'])) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }

    public static function fileName(?string $path): string
    {
        if (empty($path)) {
            return 'file';
        }
        if (preg_match('/;name=([^;]+);/i', $path, $matches)) {
            try {
                return rawurldecode($matches[1]);
            } catch (\Throwable) {
                return $matches[1];
            }
        }
        if (Str::startsWith($path, 'data:image/')) {
            $mimeSubtype = explode(';', explode('/', $path)[1] ?? 'jpeg')[0];
            return 'image.' . ($mimeSubtype ?: 'jpg');
        }
        if (Str::startsWith($path, 'data:application/pdf')) {
            return 'document.pdf';
        }
        if (Str::startsWith($path, 'data:')) {
            return 'attachment';
        }
        return basename((string) $path);
    }

    public static function fileExtension(?string $path): string
    {
        $filename = self::fileName($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($extension) {
            return $extension;
        }
        if (Str::startsWith($path, 'data:image/')) {
            return 'jpg';
        }
        if (Str::startsWith($path, 'data:application/pdf')) {
            return 'pdf';
        }
        return 'file';
    }

    public static function isImageFile(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }
        if (Str::startsWith($path, 'data:image/')) {
            return true;
        }
        $filename = self::fileName($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
    }

    public static function fileIconClass(?string $path): string
    {
        $extension = self::fileExtension($path);
        if (!$extension && Str::startsWith($path, 'data:')) {
            if (str_contains($path, 'word')) return 'fa-file-word text-blue-500';
            if (str_contains($path, 'sheet') || str_contains($path, 'excel') || str_contains($path, 'csv')) return 'fa-file-excel text-emerald-500';
            if (str_contains($path, 'zip')) return 'fa-file-archive text-amber-500';
        }
        return match ($extension) {
            'pdf' => 'fa-file-pdf text-red-500',
            'doc', 'docx' => 'fa-file-word text-blue-500',
            'xls', 'xlsx', 'csv' => 'fa-file-excel text-emerald-500',
            'zip', 'rar', '7z' => 'fa-file-archive text-amber-500',
            'txt' => 'fa-file-alt text-slate-500',
            default => 'fa-file text-slate-400',
        };
    }

    public function getPicturesAttribute(): array
    {
        if (is_array($this->picture_paths)) {
            return array_values(array_filter($this->picture_paths));
        }

        if (empty($this->picture_paths)) {
            return [];
        }

        $decoded = json_decode((string) $this->picture_paths, true);
        return is_array($decoded) ? array_values(array_filter($decoded)) : [];
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::paymentMethodLabel($this->payment_method);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::statusBadgeClass($this->status);
    }

    public function getPaymentTypeAttribute(?string $value = null): string
    {
        if (!empty($value)) {
            return $value;
        }

        $raw = $this->attributes['payment_type'] ?? null;
        if (!empty($raw)) {
            return $raw;
        }

        return in_array($this->payment_method, [self::PAYMENT_METHOD_SQUARE_CARD, self::PAYMENT_METHOD_ONLINE, 'online_payment'])
            ? self::PAYMENT_TYPE_ONLINE
            : self::PAYMENT_TYPE_IN_STORE;
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        $type = $this->payment_type;
        return self::PAYMENT_TYPE_CHOICES[$type] ?? (in_array($type, ['online', 'online_payment']) ? 'online payment' : 'In store payment');
    }

    public function hasSquareInvoice(): bool
    {
        return !empty($this->square_invoice_id);
    }

    public function isSquarePaid(): bool
    {
        return strtoupper((string) $this->square_status) === 'PAID' || $this->status === self::STATUS_PAID;
    }

    public function isPaid(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_PAID
            || strtoupper((string) $this->square_status) === 'PAID';
    }

    /**
     * Synchronize the linked repair entry status if this invoice is paid.
     */
    public function syncLinkedRepairEntry(): void
    {
        $repairId = $this->repair_entry_id;

        if (!$repairId && !empty($this->message)) {
            if (preg_match('/Repair Entry #(\d+)/i', $this->message, $m)) {
                $repairId = (int) $m[1];
                $this->repair_entry_id = $repairId;
                $this->saveQuietly();
            } elseif (preg_match('/Barcode:\s*(REP-[A-Za-z0-9_-]+)/i', $this->message, $m)) {
                $foundRepair = RepairEntry::where('item_barcode', $m[1])->first();
                if ($foundRepair) {
                    $repairId = $foundRepair->id;
                    $this->repair_entry_id = $repairId;
                    $this->saveQuietly();
                }
            }
        }

        if (!$repairId) {
            return;
        }

        $repair = RepairEntry::find($repairId);
        if (!$repair) {
            return;
        }

        if ($this->isPaid()) {
            $repair->markAsCompleted(
                RepairEntry::PAYMENT_STATUS_PAID,
                $this->payment_method
            );
        }
    }

    public static function defaultReminderMessage(self $invoice): string
    {
        $customerName = $invoice->customer_name ?: 'Valued Customer';
        $invoiceNum = $invoice->invoice_number;
        $total = '$' . number_format((float) $invoice->total, 2);
        $dueDate = $invoice->due_date?->format('F d, Y') ?: ($invoice->service_date?->format('F d, Y') ?: 'upon receipt');

        return "Hi {$customerName},\n\nThis is a friendly reminder from IT INVESTMENT RECOVERIES regarding invoice #{$invoiceNum} for {$total}, which is due {$dueDate}.\n\nPlease review your invoice and submit payment securely online using the Square payment link provided.\n\nThank you for choosing IT INVESTMENT RECOVERIES!";
    }

    public function getSquareStatusBadgeClassAttribute(): string
    {
        $status = strtoupper((string) $this->square_status);
        return match ($status) {
            'PAID' => 'bg-emerald-100 text-emerald-700',
            'UNPAID', 'SCHEDULED' => 'bg-blue-100 text-blue-700',
            'CANCELED' => 'bg-red-100 text-red-700',
            'DRAFT' => 'bg-amber-100 text-amber-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    /**
     * Synchronize inventory stock and eBay quantity for items linked to this invoice.
     * When invoice is Paid, deducts quantity from inventory and triggers eBay API sync.
     * When invoice is Unpaid/Cancelled, restores any previously deducted quantity.
     */
    public function syncInventoryStockAndEbay(): void
    {
        $this->loadMissing('items.inventory');

        $isPaid = $this->isPaid();

        $hasDeductedCol = Schema::hasColumn('sales_invoice_items', 'deducted_quantity');

        foreach ($this->items as $lineItem) {
            $inventory = $lineItem->inventory ?: ($lineItem->inventory_id ? Inventory::find($lineItem->inventory_id) : null);
            if (!$inventory) {
                continue;
            }

            $alreadyDeducted = $hasDeductedCol ? (int) ($lineItem->deducted_quantity ?? 0) : 0;

            if ($isPaid) {
                $targetDeduction = (int) $lineItem->quantity;
                $delta = $targetDeduction - $alreadyDeducted;

                if ($delta !== 0) {
                    $currentQty = (int) $inventory->quantity;
                    $newQty = max(0, $currentQty - $delta);

                    $inventory->update([
                        'quantity' => $newQty,
                    ]);

                    if ($hasDeductedCol) {
                        $lineItem->update([
                            'deducted_quantity' => $targetDeduction,
                        ]);
                    }

                    // Trigger eBay inventory quantity synchronization
                    $this->triggerEbayInventorySync($inventory->id);

                    if ($inventory->sync_status == 1) {
                        \Log::info("Synced item sold via invoice {$this->invoice_number}", [
                            'item_id' => $inventory->id,
                            'name' => $inventory->name,
                            'barcode' => $inventory->barcode,
                            'quantity_deducted' => $delta,
                            'after_quantity' => $newQty,
                            'invoice_id' => $this->id,
                        ]);
                    }
                }
            } else {
                // If invoice is not paid but was previously deducted, restore stock
                if ($alreadyDeducted > 0) {
                    $currentQty = (int) $inventory->quantity;
                    $newQty = $currentQty + $alreadyDeducted;

                    $inventory->update([
                        'quantity' => $newQty,
                    ]);

                    if ($hasDeductedCol) {
                        $lineItem->update([
                            'deducted_quantity' => 0,
                        ]);
                    }

                    // Trigger eBay inventory quantity synchronization with restored quantity
                    $this->triggerEbayInventorySync($inventory->id);

                    \Log::info("Restored stock for invoice {$this->invoice_number} marked unpaid", [
                        'item_id' => $inventory->id,
                        'restored_quantity' => $alreadyDeducted,
                        'after_quantity' => $newQty,
                        'invoice_id' => $this->id,
                    ]);
                }
            }
        }
    }

    /**
     * Restore any deducted inventory stock if invoice is deleted.
     */
    public function restoreDeductedInventoryStock(): void
    {
        $this->loadMissing('items.inventory');
        $hasDeductedCol = Schema::hasColumn('sales_invoice_items', 'deducted_quantity');

        foreach ($this->items as $lineItem) {
            $alreadyDeducted = $hasDeductedCol ? (int) ($lineItem->deducted_quantity ?? 0) : 0;
            if ($alreadyDeducted > 0) {
                $inventory = $lineItem->inventory ?: ($lineItem->inventory_id ? Inventory::find($lineItem->inventory_id) : null);
                if ($inventory) {
                    $currentQty = (int) $inventory->quantity;
                    $inventory->update([
                        'quantity' => $currentQty + $alreadyDeducted,
                    ]);

                    $this->triggerEbayInventorySync($inventory->id);
                }
            }
        }
    }

    /**
     * Trigger eBay inventory sync for a specific inventory item.
     */
    public function triggerEbayInventorySync(int $inventoryId): void
    {
        try {
            app(\App\Services\Ebay\EbayInventorySyncService::class)->syncInventoryQuantityToEbay($inventoryId);
        } catch (\Throwable $e) {
            \Log::error("eBay inventory sync failed for inventory #{$inventoryId} (Invoice #{$this->id}): " . $e->getMessage());
        }
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function repairEntry()
    {
        return $this->belongsTo(RepairEntry::class, 'repair_entry_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
