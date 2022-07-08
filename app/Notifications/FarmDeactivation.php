<?php

namespace App\Notifications;

use App\Models\Farm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FarmDeactivation extends Notification
{
    use Queueable;

    /** @var Farm */
    protected $farm;

    public function __construct(Farm $farm)
    {
        $this->farm = $farm;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $farm = $this->farm;

        return (new MailMessage)
            ->subject(__('phrases.farm_deactivation'))
            ->line(sprintf('%s: %s (%d)', __('phrases.name'), $farm->name, $farm->id))
            ->line($this->farm->deactivation_reason);
    }
}
