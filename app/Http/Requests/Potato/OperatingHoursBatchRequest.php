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
        return [
            'hours.*.type' => ['required', 'in:' . implode(',', array_keys(OperatingHour::types()))],
            'hours.*.start_date' => ['required_if:hours.*.type,==,' . OperatingHour::TYPE_DATES],
            'hours.*.end_date' => ['required_if:hours.*.type,==,' . OperatingHour::TYPE_DATES]
        ];
    }

    public function attributes(): array
    {
        return [
            'hours.*.type' => mb_strtolower(__('phrases.type')),
            'hours.*.start_date' => mb_strtolower(__('phrases.start_date')),
            'hours.*.end_date' => mb_strtolower(__('phrases.end_date'))
        ];
    }

    public function messages(): array
    {
        $attributes = $this->attributes();

        return [
            'hours.*.start_date.required_if' => __('phrases.field_name_is_required', ['field' => $attributes['hours.*.start_date']]),
            'hours.*.end_date.required_if' => __('phrases.field_name_is_required', ['field' => $attributes['hours.*.end_date']])
        ];
    }
}
