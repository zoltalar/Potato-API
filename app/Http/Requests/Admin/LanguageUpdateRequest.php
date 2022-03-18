<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LanguageUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $language = $this->route('language');
        $id = $language->id ?? null;

        return [
            'name' => ['required', 'string', 'max:100', "unique:languages,name,{$id}"],
            'native' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:2', "unique:languages,code,{$id}"]
        ];
    }

    public function attributes(): array
    {
        return ['native' => mb_strtolower(__('phrases.name_in_native_language'))];
    }
}
