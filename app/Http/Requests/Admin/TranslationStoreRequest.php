<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Translation;
use App\Rules\UniqueTranslation;

class TranslationStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'name' => ['required', 'string', "max:$length", new UniqueTranslation($this->all())],
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
