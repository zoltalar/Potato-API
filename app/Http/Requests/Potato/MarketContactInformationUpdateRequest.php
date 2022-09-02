<?php

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;

class MarketContactInformationUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'name' => ['required', "max:{$length}"],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'max:20'],
            'fax' => ['nullable', 'max:20'],
            'email' => ['required', 'email', "max:{$length}"],
            'website' => ['nullable', "max:{$length}"]
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.farm_name')),
            'first_name' => mb_strtolower(__('phrases.first_name')),
            'last_name' => mb_strtolower(__('phrases.last_name')),
            'phone' => mb_strtolower(__('phrases.phone')),
            'fax' => mb_strtolower(__('phrases.fax')),
            'email' => mb_strtolower(__('phrases.email')),
            'website' => mb_strtolower(__('phrases.website_url'))
        ];
    }
}
