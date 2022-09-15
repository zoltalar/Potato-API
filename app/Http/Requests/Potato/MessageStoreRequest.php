<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;
use App\Rules\MessageToSelf;

class MessageStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        $type = $this->route('type');
        $id = $this->route('id');

        return [
            'subject' => ['nullable', "max:{$length}", new MessageToSelf($type, $id)],
            'content' => ['required', 'max:1000']
        ];
    }

    public function attributes(): array
    {
        return [
            'subject' => mb_strtolower(__('phrases.subject')),
            'content' => mb_strtolower(__('phrases.message'))
        ];
    }
}
