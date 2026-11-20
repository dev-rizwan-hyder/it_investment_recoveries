<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepairEntry extends Model
{
    use SoftDeletes;

    public const DENVER_TIMEZONE = 'America/Denver';

    public const STATUS_REPAIR_SERVICES = 'Repair Services';
    public const STATUS_IN_STORE_PURCHASED = 'In-store Purchased';
    public const STATUS_IN_STORE_RESERVED = 'In-store Reserved';
    public const STATUS_ONLINE_PURCHASED = 'Online Purchased';
    public const STATUS_READY_FOR_PICKUP = 'Ready for Pickup';
    public const STATUS_SHIPPED = 'Shipped';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_READY_FOR_SHIPMENT = 'Ready for Shipment';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_DRAFT = 'Draft';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';
    public const PAYMENT_STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_CHOICES = [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'square_card' => 'Square Card',
    ];

    public const STATUS_CHOICES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_READY_FOR_PICKUP => 'Ready for Pickup',
        self::STATUS_READY_FOR_SHIPMENT => 'Ready for Shipment',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    public const STATUS_SELECT_CLASSES = [
        self::STATUS_REPAIR_SERVICES => 'bg-blue-50 text-blue-700 border-blue-100',
        self::STATUS_IN_STORE_PURCHASED => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        self::STATUS_IN_STORE_RESERVED => 'bg-amber-50 text-amber-700 border-amber-100',
        self::STATUS_ONLINE_PURCHASED => 'bg-teal-50 text-teal-700 border-teal-100',
        self::STATUS_READY_FOR_PICKUP => 'bg-indigo-50 text-indigo-700 border-indigo-100',
        self::STATUS_SHIPPED => 'bg-purple-50 text-purple-700 border-purple-100',
        self::STATUS_CANCELLED => 'bg-red-50 text-red-700 border-red-100',
        self::STATUS_READY_FOR_SHIPMENT => 'bg-violet-50 text-violet-700 border-violet-100',
        self::STATUS_COMPLETED => 'bg-slate-900 text-white border-slate-900',
        self::STATUS_DRAFT => 'bg-amber-50 text-amber-700 border-amber-100',
    ];

    public const STATUS_BADGE_CLASSES = [
        self::STATUS_REPAIR_SERVICES => 'bg-blue-100 text-blue-700',
        self::STATUS_IN_STORE_PURCHASED => 'bg-emerald-100 text-emerald-700',
        self::STATUS_IN_STORE_RESERVED => 'bg-amber-100 text-amber-700',
        self::STATUS_ONLINE_PURCHASED => 'bg-teal-100 text-teal-700',
        self::STATUS_READY_FOR_PICKUP => 'bg-indigo-100 text-indigo-700',
        self::STATUS_SHIPPED => 'bg-purple-100 text-purple-700',
        self::STATUS_CANCELLED => 'bg-red-100 text-red-700',
        self::STATUS_READY_FOR_SHIPMENT => 'bg-violet-100 text-violet-700',
        self::STATUS_COMPLETED => 'bg-slate-900 text-white',
        self::STATUS_DRAFT => 'bg-amber-100 text-amber-700',
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
        'technician',
        'customer_name',
        'customer_phone',
        'customer_email',
        'device_type',
        'category',
        'sub_category',
        'sub_sub_category',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'brand_category',
        'price',
        'payment_method',
        'payment_status',
        'quantity',
        'lot_info',
        'serial_number',
        'status',
        'pallet_info',
        'device_specs',
        'operating_system',
        'notes',
        'put_away_location',
        'photos',
        'item_description',
        'item_barcode',
        'cost_estimate',
        'actual_cost',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function statusChoices(): array
    {
        return self::STATUS_CHOICES;
    }

    public static function statusValues(): array
    {
        return array_unique(array_merge(
            array_keys(self::STATUS_CHOICES),
            [
                self::STATUS_DRAFT,
                self::STATUS_REPAIR_SERVICES,
                self::STATUS_IN_STORE_PURCHASED,
                self::STATUS_IN_STORE_RESERVED,
                self::STATUS_ONLINE_PURCHASED,
            ]
        ));
    }

    public static function statusLabel(?string $status): string
    {
        if (empty($status)) {
            return self::STATUS_CHOICES[self::STATUS_READY_FOR_PICKUP];
        }

        return self::STATUS_CHOICES[$status] ?? ucwords(str_replace(['-', '_'], ' ', (string) $status));
    }

    public static function statusSelectClass(?string $status): string
    {
        return self::STATUS_SELECT_CLASSES[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
    }

    public static function statusBadgeClass(?string $status): string
    {
        return self::STATUS_BADGE_CLASSES[$status] ?? 'bg-slate-100 text-slate-700';
    }

    public static function paymentMethodChoices(): array
    {
        return self::PAYMENT_METHOD_CHOICES;
    }

    public static function paymentMethodValues(): array
    {
        return array_keys(self::PAYMENT_METHOD_CHOICES);
    }

    public static function paymentMethodLabel(?string $method): string
    {
        if (empty($method)) {
            return 'N/A';
        }

        return self::PAYMENT_METHOD_CHOICES[$method] ?? ucwords(str_replace('_', ' ', (string) $method));
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

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::paymentMethodLabel($this->payment_method);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusLabel($this->payment_status);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function createdAtDenver(): ?Carbon
    {
        return $this->created_at?->copy()->timezone(self::DENVER_TIMEZONE);
    }

    public function formatCreatedAtDenver(string $format = 'M d, Y h:i A T', string $fallback = 'N/A'): string
    {
        return $this->createdAtDenver()?->format($format) ?? $fallback;
    }

    /**
     * Get the user that owns this repair entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Client / Vendor relationship
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'user_id');
    }

    /**
     * Pallet relationship
     */
    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet_info', 'barcode_number');
    }

    /**
     * Associated Sales Invoices
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'repair_entry_id');
    }

    /**
     * Get the latest sales invoice for this repair entry
     */
    public function getSalesInvoiceAttribute(): ?SalesInvoice
    {
        $invoice = SalesInvoice::where('repair_entry_id', $this->id)->latest('id')->first();
        if ($invoice) {
            return $invoice;
        }

        return SalesInvoice::where(function ($q) {
            $q->where('message', 'like', "%Repair Entry #{$this->id}%");
            if (!empty($this->item_barcode)) {
                $q->orWhere('message', 'like', "%Barcode: {$this->item_barcode}%");
            }
        })->latest('id')->first();
    }

    public function getJobNoAttribute(): ?string
    {
        if ($this->lot_info && preg_match('/Job:\s*([^\|]+)/i', $this->lot_info, $m)) {
            $val = trim($m[1]);
            if ($val !== 'N/A' && $val !== '') return $val;
        }
        return $this->pallet?->job_no;
    }

    public function getMarketingRefAttribute(): ?string
    {
        if ($this->lot_info && preg_match('/Ref:\s*(.+)$/i', $this->lot_info, $m)) {
            $val = trim($m[1]);
            if ($val !== 'N/A' && $val !== '') return $val;
        }
        return $this->pallet?->marketing_reference ?? 'No reference';
    }

    public function getReceivingEmployeeAttribute(): ?string
    {
        return $this->pallet?->receiving_employee ?? $this->technician;
    }

    public function getReceivingNotesAttribute(): ?string
    {
        if ($this->notes && preg_match('/Receiving Notes:\s*(.*?)(?=\nTech Notes:|$)/is', $this->notes, $m)) {
            return trim($m[1]);
        }
        return $this->pallet?->notes ?? $this->notes;
    }

    public function getTechnicianNotesAttribute(): ?string
    {
        if ($this->notes && preg_match('/Tech Notes:\s*(.*)/is', $this->notes, $m)) {
            return trim($m[1]);
        }
        return $this->notes;
    }

    public function getItemsListAttribute(): array
    {
        // 1. If device_specs contains JSON array of items
        if (!empty($this->device_specs)) {
            $decoded = json_decode($this->device_specs, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        // 2. If multiple records share the same pallet_info (where pallet_info is not empty)
        if (!empty($this->pallet_info)) {
            $siblings = static::where('pallet_info', $this->pallet_info)->orderBy('id')->get();
            if ($siblings->count() > 1) {
                return $siblings->map(function ($s) {
                    return [
                        'name' => $s->device_type ?: 'Repair Item',
                        'brand' => $s->brand_category ?: '',
                        'model' => $s->model_name ?? '',
                        'serial' => $s->serial_number ?: '',
                        'barcode' => $s->item_barcode ?: '',
                    ];
                })->toArray();
            }
        }

        // 3. If device_type has comma-separated items
        if (str_contains($this->device_type ?? '', ',')) {
            $names = array_map('trim', explode(',', $this->device_type ?? ''));
            $brands = array_map('trim', explode(',', $this->brand_category ?? ''));
            $serials = array_map('trim', explode(',', $this->serial_number ?? ''));
            $count = max(count($names), count($brands), count($serials));

            $items = [];
            for ($i = 0; $i < $count; $i++) {
                $items[] = [
                    'name' => $names[$i] ?? ($names[0] ?? 'Repair Item'),
                    'brand' => $brands[$i] ?? ($brands[0] ?? ''),
                    'model' => '',
                    'serial' => $serials[$i] ?? '',
                    'barcode' => ($i === 0) ? ($this->item_barcode ?? '') : '',
                ];
            }
            return $items;
        }

        // 4. Default: single item
        return [[
            'name' => $this->device_type ?: 'Repair Item',
            'brand' => $this->brand_category ?: '',
            'model' => $this->model_name ?? '',
            'serial' => $this->serial_number ?: '',
            'barcode' => $this->item_barcode ?: '',
        ]];
    }

    public function getExtraItemsCountAttribute(): int
    {
        $items = $this->items_list;
        return max(0, count($items) - 1);
    }

    public function getFirstItemNameAttribute(): string
    {
        $items = $this->items_list;
        return !empty($items[0]['name']) ? $items[0]['name'] : ($this->device_type ?: 'Repair Item');
    }

    public function getFirstItemBrandAttribute(): string
    {
        $items = $this->items_list;
        return !empty($items[0]['brand']) ? $items[0]['brand'] : ($this->brand_category ?: '');
    }

    public function getAllItemsTooltipAttribute(): string
    {
        $items = $this->items_list;
        $names = array_map(function ($it) {
            $name = $it['name'] ?? '';
            $brand = $it['brand'] ?? '';
            return trim($name . ($brand ? " ($brand)" : ""));
        }, $items);
        return implode(', ', array_filter($names));
    }

    public function getModelNameAttribute(): ?string
    {
        if ($this->item_description) {
            $desc = $this->item_description;
            if (preg_match('/^[^-]+-\s*[^\s]+\s+(.*)$/', $desc, $m)) {
                return trim($m[1]);
            }
            return $desc;
        }
        return null;
    }

    public static function parseMediaItem(string $item, int $index = 0): array
    {
        $url = str_starts_with($item, 'data:') ? $item : asset($item);

        $mime = '';
        if (str_starts_with($item, 'data:')) {
            $semicolonPos = strpos($item, ';');
            if ($semicolonPos !== false) {
                $mime = strtolower(substr($item, 5, $semicolonPos - 5));
            }
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            $mimeMap = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'webp' => 'image/webp', 'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'bmp' => 'image/bmp',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'csv' => 'text/csv', 'txt' => 'text/plain',
            ];
            $mime = $mimeMap[$ext] ?? 'application/octet-stream';
        }

        $isImage = str_starts_with($mime, 'image/') || preg_match('/\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i', $item);

        $type = 'file';
        $icon = 'fas fa-file-alt text-slate-500';
        $label = 'Document';
        $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
        $name = 'File #' . ($index + 1);

        if ($isImage) {
            $type = 'image';
            $icon = 'fas fa-file-image text-blue-500';
            $label = 'Image';
            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            $name = 'Image #' . ($index + 1);
        } elseif (str_contains($mime, 'pdf') || preg_match('/\.pdf$/i', $item)) {
            $type = 'pdf';
            $icon = 'fas fa-file-pdf text-red-600';
            $label = 'PDF';
            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
            $name = 'Document #' . ($index + 1) . '.pdf';
        } elseif (str_contains($mime, 'word') || str_contains($mime, 'document') || preg_match('/\.(doc|docx)$/i', $item)) {
            $type = 'word';
            $icon = 'fas fa-file-word text-blue-600';
            $label = 'Word';
            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            $name = 'Document #' . ($index + 1) . '.docx';
        } elseif (str_contains($mime, 'excel') || str_contains($mime, 'sheet') || str_contains($mime, 'csv') || preg_match('/\.(xls|xlsx|csv)$/i', $item)) {
            $type = 'excel';
            $icon = 'fas fa-file-excel text-emerald-600';
            $label = 'Excel/CSV';
            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $name = 'Spreadsheet #' . ($index + 1) . '.xlsx';
        }

        return [
            'raw' => $item,
            'url' => $url,
            'mime' => $mime,
            'is_image' => $isImage,
            'type' => $type,
            'icon' => $icon,
            'label' => $label,
            'badge_class' => $badgeClass,
            'name' => $name,
        ];
    }

    public function getParsedPhotosAttribute(): array
    {
        $photos = $this->photos ? json_decode($this->photos, true) : [];
        if (!is_array($photos)) {
            $photos = [];
        }

        $result = [];
        foreach ($photos as $idx => $photo) {
            if (!empty($photo)) {
                $result[] = self::parseMediaItem($photo, $idx);
            }
        }
        return $result;
    }

    public function getImagePhotosAttribute(): array
    {
        return array_values(array_filter($this->parsed_photos, fn($p) => $p['is_image']));
    }
}

