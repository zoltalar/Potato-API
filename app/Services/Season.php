<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;

final class Season
{
    const SPRING = 'spring';
    const SUMMER = 'summer';
    const FALL = 'fall';
    const WINTER = 'winter';

    const SEASONS = [
        self::SPRING,
        self::SUMMER,
        self::FALL,
        self::WINTER
    ];

    /** @var Carbon */
    protected $date;

    public function season(Carbon $date): ?string
    {
        $this->date = $date;

        return $this->get();
    }

    protected function get(): ?string
    {
        $season = null;
        $month = $this->date->month;
        $day = $this->date->day;

        if ($month == 1 || $month == 2) {
            $season = self::WINTER;
        } elseif ($month == 4 || $month == 5) {
            $season = self::SPRING;
        } elseif ($month == 7 || $month == 8) {
            $season = self::SUMMER;
        } elseif ($month == 10 || $month == 11) {
            $season = self::FALL;
        } elseif ($month == 3 && $day <= 19) {
            $season = self::WINTER;
        } elseif ($month == 3) {
            $season = self::SPRING;
        } elseif ($month == 6 && $day <= 20) {
            $season = self::SPRING;
        } elseif ($month == 6) {
            $season = self::SUMMER;
        } elseif ($month == 9 && $day <= 20) {
            $season = self::SUMMER;
        } elseif ($month == 9) {
            $season = self::FALL;
        } elseif ($month == 12 && $day <= 21) {
            $season = self::FALL;
        } elseif ($month == 12) {
            $season = self::WINTER;
        }

        return $season;
    }

    public static function order(): array
    {
        $order = [];
        $seasons = self::SEASONS;

        for ($i = 0, $length = count($seasons); $i < $length; $i++) {
            $order[$seasons[$i]] = $i;
        }

        return $order;
    }
}
