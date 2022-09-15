<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;
use App\Rules\FarmDeactivated;

class FarmDeactivateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'deactivation_reason' => ['required', 'string', 'max:500', new FarmDeactivated($id)]
        ];
    }

    public function attributes(): array
    {
        return [
            'deactivation_reason' => mb_strtolower(__('phrases.deactivation_reason'))
        ];
    }
}
