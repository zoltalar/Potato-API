<?php

declare(strict_types = 1);

namespace App\Traits;

use DateTime;
use DateTimeZone;

trait Icsable
{
    public function summary(): ?string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function startsAt(): DateTime
    {
        $startDate = $this->start_date->toDateString();
        $startTime = $this->start_time;
        $start = [$startDate];

        if ( ! empty($startTime)) {
            $start[] = $startTime;
        }

        $address = $this->address();
        $timezone = $address->timezone ?? 'Europe/Warsaw';

        return new DateTime(implode(' ', $start), new DateTimeZone($timezone));
    }

    public function endsAt(): ?DateTime
    {
        $startDate = $this->start_date->toDateString();
        $endDate = $this->end_date;
        $endTime = $this->end_time;
        $end = [$startDate];

        if ( ! empty($endDate)) {
            $end[0] = $endDate->toDateString();
        }

        if ( ! empty($endTime)) {
            $end[] = $endTime;
        }

        $address = $this->address();
        $timezone = $address->timezone ?? 'Europe/Warsaw';

        if (count($end) > 0) {
            return new DateTime(implode(' ', $end), new DateTimeZone($timezone));
        }

        return null;
    }
}
