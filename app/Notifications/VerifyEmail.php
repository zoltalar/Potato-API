<?php

declare(strict_types = 1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($notifiable instanceof User) {
            $firstName = $notifiable->first_name;
            $url = $notifiable->verificationUrl();
        }

        return (new MailMessage)
            ->subject(__('phrases.verify_email_address'))
            ->greeting(__('messages.hi_name', ['name' => $firstName ?? '']) . ',')
            ->line(__('messages.email_verify_line_1'))
            ->action(__('phrases.verify_email_address'), $url ?? '')
            ->line(__('messages.email_verify_line_2'));
    }
}
