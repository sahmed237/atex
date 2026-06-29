<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'seller_profile_id',
        'category_id',
        'name',
        'description',
        'hs_code',
        'moq',
        'available_quantity',
        'unit_price',
        'image_path',
        'seller_sku',
        'brand_name',
        'fulfillment_mode',
        'fulfillment_eligible',
        'quote_required',
        'packaging',
        'origin_lga',
        'readiness_score',
        'status',
    ];

    protected $casts = [
        'fulfillment_eligible' => 'boolean',
        'quote_required' => 'boolean',
        'readiness_score' => 'integer',
    ];

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_profile_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(FulfillmentInventory::class);
    }

    public function isExport(): bool
    {
        return $this->relationLoaded('sellerProfile')
            && $this->sellerProfile
            && $this->sellerProfile->seller_tier === 'export';
    }

    public function isLocal(): bool
    {
        return !$this->isExport();
    }
}
