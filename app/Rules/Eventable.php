<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Event;
use Illuminate\Contracts\Validation\Rule;

class Eventable implements Rule
{
    /** @var string */
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function passes($attribute, $value): bool
    {
        $ids = [];
        $type = $this->type;

        if ($type == Event::TYPE_EVENTABLE_FARM) {
            $ids = auth()->user()->farms()->pluck('id')->toArray();
        } elseif ($type == Event::TYPE_EVENTABLE_MARKET) {
            $ids = auth()->user()->markets()->pluck('id')->toArray();
        }

        if ( ! empty($value)) {
            return in_array($value, $ids);
        }

        return false;
    }

    public function message(): string
    {
        return __('messages.event_eventable_error');
    }
}
