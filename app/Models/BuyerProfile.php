<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'gender',
        'shipping_address',
        'billing_address',
        'city',
        'state',
        'zip_code',
        'country',
        'verification_status',
        'approved_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function documents()
    {
        return $this->morphMany(Document::class, 'owner');
    }
}
