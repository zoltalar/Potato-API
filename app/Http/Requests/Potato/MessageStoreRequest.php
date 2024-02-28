<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
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
            'subject' => ['nullable', "max:{$length}"],
            'content' => ['required', 'max:2000', new MessageToSelf($type, $id)]
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
