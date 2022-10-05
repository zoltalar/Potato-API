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
        return (new MailMessage)
            ->subject(__('phrases.contact'))
            ->greeting(__('phrases.hello') . ',')
            ->line(__('messages.email_contact_line_1', ['name' => $this->name(), 'email' => $this->email()]))
            ->line($this->message());
    }

    protected function name(): string
    {
        $firstName = $this->data('first_name');
        $lastName = $this->data('last_name');

        return implode(' ', [$firstName, $lastName]);
    }

    protected function email(): string
    {
        return $this->data('email');
    }

    protected function message(): string
    {
        return $this->data('message');
    }

    protected function data(string $attribute): string
    {
        $data = $this->data;

        return Arr::get($data, $attribute, '');
    }
}
