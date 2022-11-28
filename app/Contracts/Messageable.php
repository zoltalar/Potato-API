<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\Models\User;

interface Messageable
{
    public function recipient(): ?User;
}
