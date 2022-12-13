<?php

namespace App\Services;

use App\Contracts\Icsable;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

final class Ics
{
    /** @var Icsable */
    protected $icsable;

    public function __construct(Icsable $icsable)
    {
        $this->icsable = $icsable;
    }

    public function __toString()
    {
        $icsable = $this->icsable;

        $event = Event::create()
            ->name($icsable->getSummary())
            ->description($icsable->getDescription());

        $address = $this->icsable->address();

        if ($address !== null) {
            $event->address($address->addressLine());
        }

        $event
            ->startsAt($icsable->startsAt())
            ->endsAt($icsable->endsAt())
            ->alertMinutesBefore(1440, __('phrases.event'));

        return Calendar::create()
            ->productIdentifier(__('messages.codename'))
            ->event($event)
            ->get();
    }
}
