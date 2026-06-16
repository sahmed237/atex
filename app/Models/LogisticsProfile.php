<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'registration_number',
        'tax_number',
        'bvn',
        'nin',
        'address',
        'coverage_regions',
        'transport_modes',
        'base_location',
        'fleet_capacity',
        'verification_status',
        'readiness_score',
        'approved_at',
        'bank_name',
        'account_number',
        'account_name',
        'fleet_size',
        'rejection_reason',
        'regulatory_reviews',
    ];

    protected $casts = [
        'regulatory_reviews' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'owner');
    }
}
