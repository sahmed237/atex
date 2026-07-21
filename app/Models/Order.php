<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'quote_request_id',
        'product_id',
        'buyer_profile_id',
        'seller_profile_id',
        'order_quantity',
        'destination_location',
        'total_amount',
        'currency',
        'fulfillment_mode',
        'fulfillment_status',
        'commission_amount',
        'tax_amount',
        'net_payout_amount',
        'settlement_status',
        'payment_status',
        'shipment_status',
        'status',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_payout_amount' => 'decimal:2',
    ];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function buyerProfile(): BelongsTo
    {
        return $this->belongsTo(BuyerProfile::class);
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(Settlement::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }
}
