<?php

declare(strict_types = 1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Addressable
{
    public function addressLine(string $glue = ',', array $elements = []): string;

    public function state(): BelongsTo;
}
