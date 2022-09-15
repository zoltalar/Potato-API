<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;

class FarmDescriptionUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['description' => ['nullable', 'max:5000']];
    }

    public function attributes(): array
    {
        return ['description' => mb_strtolower(__('phrases.description'))];
    }
}
