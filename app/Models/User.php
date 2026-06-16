<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'passport',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'require_password_change',
        'is_active',
        'email_verified_at',
        'locked_until',
        'bvn',
        'nin',
        'bank_name',
        'account_number',
        'account_name',
        'kyc_verification_status',
        'kyc_submitted_at',
        'kyc_approved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password' => 'hashed',
            'require_password_change' => 'boolean',
            'is_active' => 'boolean',
            'locked_until' => 'datetime',
            'kyc_submitted_at' => 'datetime',
            'kyc_approved_at' => 'datetime',
        ];
    }
    /**
     * Determine if the user has 2FA enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the recovery codes for the user.
     */
    public function recoveryCodes(): array
    {
        return $this->two_factor_recovery_codes 
            ? json_decode(decrypt($this->two_factor_recovery_codes), true) 
            : [];
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        }

        $this->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ])->save();

        return $codes;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        \Illuminate\Support\Facades\Mail::to($this)->send(new \App\Mail\PasswordResetRequestMail($this, $token));
    }

    /**
     * Check if user is exempt from 2FA
     */
    public function isTwoFactorExempt(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function exporterProfile()
    {
        return $this->hasOne(ExporterProfile::class);
    }

    public function buyerProfile()
    {
        return $this->hasOne(BuyerProfile::class);
    }

    public function logisticsProfile()
    {
        return $this->hasOne(LogisticsProfile::class);
    }

    public function fieldOfficerProfile()
    {
        return $this->hasOne(FieldOfficerProfile::class);
    }

    public function kycProfile()
    {
        if ($this->hasRole('exporter')) {
            return $this->exporterProfile();
        }
        if ($this->hasRole('buyer')) {
            return $this->buyerProfile();
        }
        if ($this->hasRole('logistics')) {
            return $this->logisticsProfile();
        }
        if ($this->hasRole('field-officer')) {
            return $this->fieldOfficerProfile();
        }
        return null;
    }
}
