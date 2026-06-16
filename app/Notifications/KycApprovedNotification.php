<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KycApprovedNotification extends Notification
{
    use Queueable;

    public User $user;
    public string $profileType;

    public function __construct(User $user, string $profileType)
    {
        $this->user = $user;
        $this->profileType = $profileType;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $roleLabel = ucfirst($this->profileType);

        return (new MailMessage)
            ->subject('Your KYC Application Has Been Approved')
            ->greeting("Hello {$this->user->name}!")
            ->line("Congratulations! Your {$roleLabel} KYC application has been approved.")
            ->line('You can now access all features of your account on the Adamawa Export Market platform.')
            ->action('Go to Dashboard', url('/dashboard'))
            ->line('Thank you for joining us!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'KYC Approved',
            'message' => 'Your KYC application has been approved. You can now access all platform features.',
            'profile_type' => $this->profileType,
        ];
    }
}
