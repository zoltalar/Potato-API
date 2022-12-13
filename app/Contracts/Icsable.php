<?php

declare(strict_types = 1);

namespace App\Contracts;

use DateTime;

interface Icsable
{
    public function getSummary(): ?string;

    public function getDescription(): ?string;

    public function startsAt(): DateTime;

    public function endsAt(): ?DateTime;
}
