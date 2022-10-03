<?php

declare(strict_types = 1);

namespace App\Notifications;

use App\Models\Market;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketDeactivation extends Notification
{
    use Queueable;

    /** @var Market */
    protected $market;

    public function __construct(Market $market)
    {
        $this->market = $market;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $market = $this->market;

        return (new MailMessage)
            ->subject(__('phrases.farmers_market_deactivation'))
            ->greeting(__('phrases.hello') . ',')
            ->line(sprintf('%s: %s (%d)', __('phrases.name'), $market->name, $market->id))
            ->line($market->deactivation_reason);
    }
}
