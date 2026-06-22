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
            ->mailer('smtp_kyc')
            ->from('kyc@atex.adamawastate.gov.ng', 'Adamawa Ecommerce platform KYC')
            ->subject('Your Account Verification Needs Attention')
            ->greeting("Hello {$this->user->name}!")
            ->line("Your {$roleLabel} profile verification has been reviewed, but we need you to address a few things before we can approve it.");

        if ($this->reason) {
            $message->line("Reason: {$this->reason}");
        }

        $message->line('Please review your profile information, update your documents, and resubmit.')
            ->action('Review Verification Status', url('/kyc/onboarding'))
            ->line('If you have any questions, please contact our support team.');

        return $message;
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Verification Needs Attention',
            'message' => 'Your profile verification requires attention. Please review and resubmit.',
            'profile_type' => $this->profileType,
            'reason' => $this->reason,
        ];
    }
}
