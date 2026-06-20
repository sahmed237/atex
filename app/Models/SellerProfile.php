<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'business_description',
        'business_category',
        'registration_number',
        'tax_number',
        'bvn',
        'nin',
        'business_type',
        'lga',
        'address',
        'seller_program_status',
        'seller_brand_name',
        'fulfillment_model',
        'verification_status',
        'seller_tier',
        'readiness_score',
        'approved_at',
        'bank_name',
        'account_number',
        'account_name',
        'trade_capacity',
        'years_of_experience',
        'export_markets',
        'rejection_reason',
        'regulatory_reviews',
        'country',
        'state',
        'city',
        'phone',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'readiness_score' => 'integer',
        'regulatory_reviews' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(FulfillmentInventory::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'owner');
    }
}
