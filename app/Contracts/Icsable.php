<?php

declare(strict_types = 1);

namespace App\Contracts;

use DateTime;

interface Icsable
{
    public function summary(): ?string;

    public function description(): ?string;

    public function startsAt(): DateTime;

    public function endsAt(): ?DateTime;
}
