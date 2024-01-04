<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\AddressOwner;

class AddressRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->route('type');
        $id = $this->route('id');
        
        return [
            'address.country_id' => ['required', 'exists:countries,id', new AddressOwner($type, $id)],
            'address.state_id' => ['required', 'exists:states,id'],
            'address.address' => ['required', 'max:100'],
            'address.city' => ['required', 'max:60'],
            'address.zip' => ['required', 'max:15']
        ];
    }

    public function attributes(): array
    {
        return [
            'address.country_id' => mb_strtolower(__('phrases.country')),
            'address.state_id' => mb_strtolower(__('phrases.state')),
            'address.address' => mb_strtolower(__('phrases.address')),
            'address.city' => mb_strtolower(__('phrases.town')),
            'address.zip' => mb_strtolower(__('phrases.postal_code'))
        ];
    }

    public function messages(): array
    {
        return [
            'address.state_id.required' => __('phrases.this_field_is_required'),
            'address.address.required' => __('phrases.this_field_is_required')
        ];
    }
}
