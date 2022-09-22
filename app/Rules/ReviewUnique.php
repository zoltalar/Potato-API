<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Farm;
use App\Models\Market;
use App\Models\Message;
use App\Models\Review;
use Illuminate\Contracts\Validation\Rule;

class ReviewUnique implements Rule
{
    /** @var string */
    protected $type;

    /** @var int */
    protected $id;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function passes($attribute, $value): bool
    {
        $type = $this->type;
        $id = $this->id;
        $rateable = null;

        if ($type === Review::TYPE_RATEABLE_FARM) {
            $rateable = Farm::find($id);
        } elseif ($type === Review::TYPE_RATEABLE_MARKET) {
            $rateable = Market::find($id);
        }

        return ($rateable !== null && $rateable->review(auth()->user()) === null);
    }

    public function message(): string
    {
        return __('messages.review_unique_error');
    }
}
