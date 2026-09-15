<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'company', 'address'];

    /**
     * Get the pallets for the client.
     */
    public function pallets(): HasMany
    {
        return $this->hasMany(Pallet::class);
    }
}