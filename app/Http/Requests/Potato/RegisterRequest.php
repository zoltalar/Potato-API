<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;

class RegisterRequest extends BaseRequest
{
    public function validationData(): array
    {
        $language = Language::query()
            ->where('code', $this->header('X-language'))
            ->first();

        $country = Country::query()
            ->where('code', $this->header('X-country'))
            ->first();

        $extra = [
            'language_id' => $language->id ?? null,
            'country_id' => $country->id ?? null
        ];

        return $this->merge($extra)->toArray();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', "max:{$length}", 'unique:users,email'],
            'password' => ['required', 'min:7']
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => mb_strtolower(__('phrases.first_name')),
            'last_name' => mb_strtolower(__('phrases.last_name')),
            'email' => mb_strtolower(__('phrases.email')),
            'password' => mb_strtolower(__('phrases.password'))
        ];
    }
}
