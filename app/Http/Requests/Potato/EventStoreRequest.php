<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Event;
use App\Models\Language;
use App\Rules\Eventable;
use Illuminate\Validation\Rule;

class EventStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;
        $type = $this->eventable_type;

        return [
            'title' => ['required', "max:{$length}"],
            'eventable_id' => ['required', new Eventable($type)],
            'eventable_type' => ['required', Rule::in(array_keys(Event::eventableTypes()))]
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => mb_strtolower(__('phrases.title')),
            'eventable_id' => mb_strtolower(__('phrases.organizer')),
            'eventable_type' => mb_strtolower(__('phrases.type'))
        ];
    }
}
