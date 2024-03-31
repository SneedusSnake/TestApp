<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime: Y-m-d H:i:s',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(ClientEmail::class);
    }

    public function websites(): HasMany
    {
        return $this->hasMany(ClientWebsite::class);
    }
}
