<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldOfficerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address',
        'bvn',
        'nin',
        'bank_name',
        'account_number',
        'account_name',
        'identification_number',
        'verification_status',
        'approved_at',
        'rejection_reason',
        'regulatory_reviews',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'regulatory_reviews' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'owner');
    }
}
