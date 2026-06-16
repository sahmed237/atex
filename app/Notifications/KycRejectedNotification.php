<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KycRejectedNotification extends Notification
{
    use Queueable;

    public User $user;
    public string $profileType;
    public ?string $reason;

    public function __construct(User $user, string $profileType, ?string $reason = null)
    {
        $this->user = $user;
        $this->profileType = $profileType;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $roleLabel = ucfirst($this->profileType);

        $message = (new MailMessage)
            ->subject('Your KYC Application Needs Attention')
            ->greeting("Hello {$this->user->name}!")
            ->line("Your {$roleLabel} KYC application has been reviewed and was not approved at this time.");

        if ($this->reason) {
            $message->line("Reason: {$this->reason}");
        }

        $message->line('Please review your information and submit a new KYC application.')
            ->action('Complete KYC', url('/kyc/onboarding'))
            ->line('If you have any questions, please contact our support team.');

        return $message;
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'KYC Rejected',
            'message' => 'Your KYC application was not approved. Please resubmit with correct information.',
            'profile_type' => $this->profileType,
            'reason' => $this->reason,
        ];
    }
}
