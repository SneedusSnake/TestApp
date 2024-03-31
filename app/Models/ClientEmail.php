<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientEmail extends Model
{
    use HasFactory;
    protected $fillable = ['is_main', 'email'];

    public $timestamps = false;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
