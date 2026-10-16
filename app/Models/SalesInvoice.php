<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_CHEQUE = 'cheque';
    public const PAYMENT_METHOD_SQUARE_CARD = 'square_card';

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_CHOICES = [
        self::PAYMENT_METHOD_CASH => 'Cash',
        self::PAYMENT_METHOD_CHEQUE => 'Cheque',
        self::PAYMENT_METHOD_SQUARE_CARD => 'Square Card',
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
    ];

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
        return array_keys(self::PAYMENT_METHOD_CHOICES);
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

        if (Str::startsWith($path, ['sales/', 'storage/']) && file_exists(public_path($path))) {
            return asset($path);
        }

        // Check if file exists in it_investment public folder
        $itInvestmentPath = '/opt/lampp/htdocs/it_investment/public/' . $path;
        if (file_exists($itInvestmentPath) && is_file($itInvestmentPath)) {
            $mime = mime_content_type($itInvestmentPath) ?: 'application/octet-stream';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($itInvestmentPath));
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

    public function getPaymentTypeAttribute(): string
    {
        return $this->payment_method === self::PAYMENT_METHOD_SQUARE_CARD ? 'online' : 'in_store';
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return $this->payment_type === 'online' ? 'Online payment' : 'In store payment';
    }

    public function hasSquareInvoice(): bool
    {
        return !empty($this->square_invoice_id);
    }

    public function isSquarePaid(): bool
    {
        return strtoupper((string) $this->square_status) === 'PAID' || $this->status === self::STATUS_PAID;
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
}
