<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Event;
use App\Models\Language;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'title' => ['required', "max:{$length}"],
            'website' => ['nullable', "max:{$length}"],
            'phone' => ['nullable', 'max:20'],
            'email' => ['required', 'email', "max:{$length}"],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable', 'after:start_time'],
            'description' => ['nullable', 'max:5000'],
            'status' => ['required', Rule::in(array_keys(Event::statuses()))]
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => mb_strtolower(__('phrases.title')),
            'website' => mb_strtolower(__('phrases.website_url')),
            'phone' => mb_strtolower(__('phrases.phone')),
            'email' => mb_strtolower(__('phrases.email')),
            'start_date' => mb_strtolower(__('phrases.start_date')),
            'end_date' => mb_strtolower(__('phrases.end_date')),
            'start_time' => mb_strtolower(__('phrases.start_time')),
            'end_time' => mb_strtolower(__('phrases.end_time')),
            'description' => mb_strtolower(__('phrases.description')),
            'status' => mb_strtolower(__('phrases.status'))
        ];
    }
}
