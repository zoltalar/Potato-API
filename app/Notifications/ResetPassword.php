<?php

declare(strict_types = 1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    /** @var string */
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($notifiable instanceof User) {
            $token = $this->token;
            $url = $notifiable->passwordResetUrl($token);
        }

        return (new MailMessage)
            ->subject(__('phrases.reset_password'))
            ->greeting(__('phrases.hello') . ',')
            ->line(__('messages.email_reset_password_line_1'))
            ->action(__('phrases.reset_password'), $url ?? '');
    }
}
