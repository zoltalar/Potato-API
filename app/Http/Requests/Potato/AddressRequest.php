<?php

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;

class AddressRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state_id' => ['required', 'exists:states,id'],
            'address' => ['required', 'max:100'],
            'city' => ['required', 'max:60'],
            'zip' => ['required', 'max:15']
        ];
    }

    public function attributes(): array
    {
        return [
            'state_id' => mb_strtolower(__('phrases.state')),
            'address' => mb_strtolower(__('phrases.address')),
            'city' => mb_strtolower(__('phrases.town')),
            'zip' => mb_strtolower(__('phrases.postal_code'))
        ];
    }

    public function messages(): array
    {
        return [
            'state_id.required' => __('phrases.this_field_is_required'),
            'address.required' => __('phrases.this_field_is_required')
        ];
    }
}
