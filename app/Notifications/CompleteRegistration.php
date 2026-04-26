<?php

namespace App\Notifications;

use App\Models\PendingRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CompleteRegistration extends Notification
{
    use Queueable;

    public function __construct(
        public PendingRegistration $pendingRegistration,
        public string $token,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Complete your Pelicon registration')
            ->greeting('Complete your Pelicon registration')
            ->line('Use the button below to verify your email address and finish creating your account.')
            ->action('Complete registration', $this->verificationUrl())
            ->line('This link expires in 60 minutes. If you did not request this email, you can ignore it.');
    }

    public function verificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'register.complete',
            $this->pendingRegistration->expires_at,
            [
                'pendingRegistration' => $this->pendingRegistration,
                'token' => $this->token,
            ],
        );
    }
}
