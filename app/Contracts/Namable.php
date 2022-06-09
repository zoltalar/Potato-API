<?php

declare(strict_types = 1);

namespace App\Contracts;

interface Namable
{
    public function fullName(bool $standard = true): string;
}
