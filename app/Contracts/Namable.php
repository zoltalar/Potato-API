<?php

declare(strict_types = 1);

namespace App\Contracts;

interface Namable
{
    /**
     * Retrieve person's full name.
     *
     * @param   bool $standard
     * @return  string
     */
    public function fullName(bool $standard = true): string;
}