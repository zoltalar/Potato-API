<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\OperatingHour;

class OperatingHoursBatchRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'hours.*.type' => ['required', 'in:' . implode(',', array_keys(OperatingHour::types()))],
            'hours.*.start_date' => ['required_if:hours.*.type,==,' . OperatingHour::TYPE_DATES],
            'hours.*.end_date' => ['required_if:hours.*.type,==,' . OperatingHour::TYPE_DATES],
            'hours.*.start_month' => ['required_if:hours.*.type,==,' . OperatingHour::TYPE_MONTHS],
            'hours.*.end_month' => ['required_if:hours.*.type,==,' . OperatingHour::TYPE_MONTHS]
        ];

        $hours = $this->get('hours', []);

        if (is_array($hours)) {

            foreach ($hours as $i => $hour) {

                foreach (OperatingHour::days() as $day) {
                    $rules["hours.{$i}.{$day}.start"] = ['nullable', "required_if:hours.{$i}.{$day}.selected,==,true"];
                    $rules["hours.{$i}.{$day}.end"] = ['nullable', "required_if:hours.{$i}.{$day}.selected,==,true", "after:hours.{$i}.{$day}.start"];
                }
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'hours.*.type' => mb_strtolower(__('phrases.type')),
            'hours.*.start_date' => mb_strtolower(__('phrases.start_date')),
            'hours.*.end_date' => mb_strtolower(__('phrases.end_date')),
            'hours.*.start_month' => mb_strtolower(__('phrases.start_month')),
            'hours.*.end_month' => mb_strtolower(__('phrases.end_month'))
        ];

        $hours = $this->get('hours', []);

        if (is_array($hours)) {

            foreach ($hours as $i => $hour) {

                foreach (OperatingHour::days() as $day) {
                    $attributes["hours.{$i}.{$day}.start"] = mb_strtolower(__('phrases.start_time'));
                    $attributes["hours.{$i}.{$day}.end"] = mb_strtolower(__('phrases.end_time'));
                }
            }
        }

        return $attributes;
    }

    public function messages(): array
    {
        $attributes = $this->attributes();

        $messages = [
            'hours.*.start_date.required_if' => __('phrases.field_name_is_required', ['field' => $attributes['hours.*.start_date']]),
            'hours.*.end_date.required_if' => __('phrases.field_name_is_required', ['field' => $attributes['hours.*.end_date']]),
            'hours.*.start_month.required_if' => __('phrases.field_name_is_required', ['field' => $attributes['hours.*.start_month']]),
            'hours.*.end_month.required_if' => __('phrases.field_name_is_required', ['field' => $attributes['hours.*.end_month']])
        ];

        $hours = $this->get('hours', []);

        if (is_array($hours)) {

            foreach ($hours as $i => $hour) {

                foreach (OperatingHour::days() as $day) {
                    $messages["hours.{$i}.{$day}.start.required_if"] = __('phrases.field_name_is_required', ['field' => $attributes["hours.{$i}.{$day}.start"]]);
                    $messages["hours.{$i}.{$day}.end.required_if"] = __('phrases.field_name_is_required', ['field' => $attributes["hours.{$i}.{$day}.end"]]);

                    $messages["hours.{$i}.{$day}.end.after"] = __('messages.time_field_after_error', [
                        'field' => $attributes["hours.{$i}.{$day}.start"],
                        'time' => $attributes["hours.{$i}.{$day}.end"]
                    ]);
                }
            }
        }

        return $messages;
    }
}
