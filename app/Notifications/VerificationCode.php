<?php

declare(strict_types = 1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCode extends Notification
{
    use Queueable;

    /** @var string */
    protected $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $code = $this->code;

        if ($notifiable instanceof User) {
            $firstName = $notifiable->first_name;
        }

        return (new MailMessage)
            ->subject(__('phrases.verify_email_address'))
            ->greeting(__('messages.hi_name', ['name' => $firstName ?? '']) . ',')
            ->line(__('messages.email_verification_code_line_1'))
            ->line(sprintf('<strong>%s</strong>', $code))
            ->line(__('messages.email_verification_code_line_2'));
    }
}
