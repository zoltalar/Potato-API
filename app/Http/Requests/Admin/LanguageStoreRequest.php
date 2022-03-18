<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LanguageStoreRequest extends FormRequest
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
        return ['native' => mb_strtolower(__('phrases.name_in_native_language'))];
    }
}
