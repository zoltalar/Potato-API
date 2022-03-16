<?php

declare(strict_types = 1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Addressable
{
    /**
     * Retrieve formatted address line.
     *
     * @param   string $glue
     * @param   array $elements
     * @return  string
     */
    public function addressLine(string $glue = ',', array $elements = []): string;

    /**
     * Get related state model.
     *
     * @return  BelongsTo
     */
    public function state(): BelongsTo;
}
