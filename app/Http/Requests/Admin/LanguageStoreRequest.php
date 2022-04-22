<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class LanguageStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:languages,name'],
            'native' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:2', 'unique:languages,code']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.name')),
            'native' => mb_strtolower(__('phrases.name_in_native_language')),
            'code' => mb_strtolower(__('phrases.code'))
        ];
    }
}
