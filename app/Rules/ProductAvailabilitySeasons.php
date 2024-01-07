<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Services\Season;
use Illuminate\Contracts\Validation\Rule;

class ProductAvailabilitySeasons implements Rule
{
    /** @var array */
    protected $product;

    public function __construct(?array $product)
    {
        $this->product = $product;
    }

    public function passes($attribute, $value): bool
    {
        $product = $this->product;
        $seasons = Season::SEASONS;
        $count = 0;

        foreach ($seasons as $season) {
            if (isset($product[$season]) && $product[$season] == 1) {
                $count++;
            }
        }

        return $count > 0;
    }

    public function message(): string
    {
        return __('messages.product_availability_seasons_error');
    }
}
