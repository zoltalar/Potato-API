<?php

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;

class FarmOperatingHoursUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['operating_hours' => ['nullable', 'max:500']];
    }

    public function attributes(): array
    {
        return ['operating_hours' => mb_strtolower(__('phrases.operating_hours'))];
    }
}
