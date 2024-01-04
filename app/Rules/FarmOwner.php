<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Farm;
use Illuminate\Contracts\Validation\Rule;

class FarmOwner implements Rule
{
    /** @var Farm */
    protected $farm;

    public function __construct($id)
    {
        $this->farm = Farm::find($id);
    }

    public function passes($attribute, $value): bool
    {
        return auth()->id() === $this->farm->user_id;
    }

    public function message(): string
    {
        return __('messages.farm_owner_error');
    }
}
