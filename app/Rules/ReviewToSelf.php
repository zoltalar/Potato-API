<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Farm;
use App\Models\Message;
use App\Models\Review;
use Illuminate\Contracts\Validation\Rule;

class ReviewToSelf implements Rule
{
    /** @var string */
    protected $type;

    /** @var int */
    protected $id;

    public function __construct(?string $type, ?int $id)
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
        }

        return ($rateable !== null && $rateable->user_id != auth()->id());
    }

    public function message(): string
    {
        return __('messages.review_to_self_error');
    }
}
