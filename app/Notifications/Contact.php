<?php

declare(strict_types = 1);

namespace App\Notifications;

use Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Contact extends Notification
{
    use Queueable;

    /** @var array */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $data = $this->data;

        $firstName = Arr::get($data, 'first_name');
        $lastName = Arr::get($data, 'last_name');
        $name = implode(' ', [$firstName, $lastName]);
        $email = Arr::get($data, 'email');
        $message = Arr::get($data, 'message');

        return (new MailMessage)
            ->subject(__('phrases.contact'))
            ->greeting(__('phrases.hello') . ',')
            ->line(__('messages.email_contact_line_1', ['name' => $name, 'email' => $email]))
            ->line($message);
    }
}
