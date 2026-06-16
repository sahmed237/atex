<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $profileType;
    public $profile;
    public ?string $reason;
    public ?string $reviewedBy;

    public function __construct(User $user, string $profileType, $profile, ?string $reason = null, ?string $reviewedBy = null)
    {
        $this->user = $user;
        $this->profileType = $profileType;
        $this->profile = $profile;
        $this->reason = $reason;
        $this->reviewedBy = $reviewedBy;
    }
}
