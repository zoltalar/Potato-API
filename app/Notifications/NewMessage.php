<?php

declare(strict_types = 1);

namespace App\Notifications;

use App\Models\User;
use App\Services\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    /** @var User */
    protected $sender;

    /** @var User */
    protected $recipient;

    public function __construct(User $sender, User $recipient)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $sender = $this->sender;
        $recipient = $this->recipient;

        return (new MailMessage)
            ->subject(__('phrases.new_message'))
            ->greeting(__('messages.hi_name', ['name' => $recipient->first_name]))
            ->line(__('messages.email_new_message_line_1', ['name' => $sender->fullName()]))
            ->action(__('phrases.read_message'), Url::appBaseUrl());
    }
}
