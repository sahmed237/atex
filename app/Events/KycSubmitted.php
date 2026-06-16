<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $profileType;
    public $profile;

    public function __construct(User $user, string $profileType, $profile)
    {
        $this->user = $user;
        $this->profileType = $profileType;
        $this->profile = $profile;
    }
}
