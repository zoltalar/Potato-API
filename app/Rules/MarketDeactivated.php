<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Market;
use Illuminate\Contracts\Validation\Rule;

class MarketDeactivated implements Rule
{
    /** @var Market */
    protected $market;

    public function __construct($id)
    {
        $this->market = Market::find($id);
    }

    public function passes($attribute, $value): bool
    {
        $market = $this->market;

        if ($market !== null) {
            return $market->active == 1;
        }

        return false;
    }

    public function message(): string
    {
        return __('messages.market_deactivated_error');
    }
}
