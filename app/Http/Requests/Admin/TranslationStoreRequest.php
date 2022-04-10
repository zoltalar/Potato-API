<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Models\Base;
use App\Models\Translation;
use Illuminate\Foundation\Http\FormRequest;

class TranslationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'name' => ['required', 'string', "max:$length"],
            'language_id' => ['required', 'exists:languages,id'],
            'translatable_type' => ['required', 'in:' . implode(',', array_keys(Translation::types()))],
            'translatable_id' => ['required']
        ];
    }

    public function attributes(): array
    {
        return ['language_id' => mb_strtolower(__('phrases.language'))];
    }
}
