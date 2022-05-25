<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;

class ImageStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'title' => ['nullable', "max:{$length}"],
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120']
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => mb_strtolower(__('phrases.title')),
            'file' => mb_strtolower(__('phrases.photo'))
        ];
    }
}
