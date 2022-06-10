<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Farm;
use Illuminate\Contracts\Validation\Rule;

class FarmDeactivated implements Rule
{
    /** @var Farm */
    protected $farm;

    public function __construct(?int $id)
    {
        $this->farm = Farm::find($id);
    }

    public function passes($attribute, $value): bool
    {
        $farm = $this->farm;

        if ($farm !== null) {
            return $farm->active == 1;
        }

        return false;
    }

    public function message(): string
    {
        return __('messages.farm_deactivated_error');
    }
}
