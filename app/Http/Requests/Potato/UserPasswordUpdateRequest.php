<?php

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;

class UserPasswordUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password_current' => ['required'],
            'password' => ['required', 'min:7', 'confirmed']
        ];
    }

    public function attributes(): array
    {
        return [
            'password_current' => mb_strtolower(__('phrases.current_password')),
            'password' => mb_strtolower(__('phrases.new_password'))
        ];
    }
}
