<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\OperatingHour;

class OperatingHoursRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        foreach (OperatingHour::days() as $day) {
            $rules[$day . '.start'] = ['nullable', 'required_if:' . $day . '.selected,==,true'];
            $rules[$day . '.end'] = ['nullable', 'required_if:' . $day . '.selected,==,true', 'after:' . $day . '.start'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (OperatingHour::days() as $day) {
            $attributes[$day . '.start'] = mb_strtolower(__('phrases.start_time'));
            $attributes[$day . '.end'] = mb_strtolower(__('phrases.end_time'));
        }

        return $attributes;
    }

    public function messages(): array
    {
        $messages = [];
        $attributes = $this->attributes();

        foreach (OperatingHour::days() as $day) {
            $messages[$day . '.start.required_if'] = __('phrases.field_name_is_required', ['field' => $attributes[$day . '.start']]);
            $messages[$day . '.end.required_if'] = __('phrases.field_name_is_required', ['field' => $attributes[$day . '.end']]);

            $messages[$day . '.end.after'] = __('messages.time_field_after_error', [
                'field' => $attributes[$day . '.start'],
                'time' => $attributes[$day . '.end']
            ]);
        }

        return $messages;
    }
}
