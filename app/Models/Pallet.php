<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'barcode_number',
        'client_id',
        'status',
        'put_away_location',
        'marketing_reference',
        'description',
        'estimated_count',
        'reuse_quantity',
        'gross_weight',
        'tare_weight',
        'reuse_weight',
        'net_weight',
        'notes',
        'receiving_employee',
        'job_no',
        'processing_employee',
        'intake_employee',
        'ecommerce_employee',
        'refurbishing_employee',
        'universal_waste_employee',
        'data_destruction_employee',
        'photos_paths',
        'created_at',
        'updated_at'
    ];

    /**
     * Add this casts property to handle the Array to String error
     */
    protected $casts = [
        'photos_paths' => 'array', // Yeh array ko JSON mein convert karega automatically
    ];

    /**
     * Get the pallet's status, defaulting 'pending' or empty values to 'Received'.
     */
    public function getStatusAttribute($value): string
    {
        if (empty($value) || strtolower($value) === 'pending') {
            return 'Received';
        }
        if (strtolower($value) === 'draft') {
            return 'Draft';
        }
        return $value;
    }

    /**
     * Boot the model - ensure clean data
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Ensure photos_paths is always a clean array without false/null values
            if (is_array($model->photos_paths)) {
                $model->photos_paths = array_filter($model->photos_paths, function($path) {
                    return !empty($path) && $path !== false && is_string($path);
                });
                // Re-index array to avoid JSON objects
                $model->photos_paths = array_values($model->photos_paths);
            } elseif (!is_array($model->photos_paths)) {
                $model->photos_paths = [];
            }
        });
    }

    /**
     * Get the client that owns the pallet.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    /**
     * Get formatted display barcode with duplicate sequence number e.g. PAL-7525830626 (#1).
     */
    public function getDisplayBarcode(): string
    {
        $barcode = $this->barcode_number;
        if (empty($barcode)) {
            return 'N/A';
        }

        $query = static::where('barcode_number', $barcode);
        if ($this->client_id) {
            $query->where('client_id', $this->client_id);
        }

        $allIds = $query->orderBy('id', 'asc')->pluck('id');

        if ($allIds->count() > 1) {
            $index = $allIds->search($this->id);
            if ($index !== false) {
                return "{$barcode} (#" . ($index + 1) . ")";
            }
        }

        return $barcode;
    }

    /**
     * Get all related entries for this pallet (sharing the same barcode and client).
     */
    public function getRelatedEntries()
    {
        $barcode = trim((string) ($this->barcode_number ?? ''));
        if ($barcode === '') {
            return collect([$this]);
        }

        $query = static::whereRaw('TRIM(barcode_number) = ?', [$barcode]);
        if ($this->client_id) {
            $query->where('client_id', $this->client_id);
        } else {
            $query->whereNull('client_id');
        }

        return $query->orderBy('id', 'asc')->get();
    }

    /**
     * Check if this pallet belongs to a group with multiple entries (#1, #2, #3...).
     */
    public function hasMultipleEntries(): bool
    {
        return $this->getRelatedEntries()->count() > 1;
    }

    /**
     * Check if all entries for this pallet (#1, #2, #3...) are marked Completed.
     */
    public function areAllEntriesCompleted(): bool
    {
        $entries = $this->getRelatedEntries();

        if ($entries->isEmpty()) {
            return false;
        }

        return $entries->every(function ($entry) {
            $status = ($entry->id === $this->id) ? $this->status : $entry->status;
            return $status === 'Completed';
        });
    }
}