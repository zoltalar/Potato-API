<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Services\Request\CountryRequestHeader;
use App\Services\Request\LimitRequestVar;

trait SearchableRequest
{
    protected function country(): string
    {
        return (string) (new CountryRequestHeader())->get();
    }

    protected function limit(): int
    {
        return (int) (new LimitRequestVar())->get();
    }
}
