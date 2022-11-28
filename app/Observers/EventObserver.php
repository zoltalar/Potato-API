<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    public function deleted(Event $event)
    {
        $event->addresses()->delete();
    }
}
