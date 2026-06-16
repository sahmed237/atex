<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $profileType;
    public $profile;
    public ?string $reviewedBy;

    public function __construct(User $user, string $profileType, $profile, ?string $reviewedBy = null)
    {
        $this->user = $user;
        $this->profileType = $profileType;
        $this->profile = $profile;
        $this->reviewedBy = $reviewedBy;
    }
}
