<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;

class MessageReplyRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['content' => ['required', 'max:2000']];
    }

    public function attributes(): array
    {
        return ['content' => mb_strtolower(__('phrases.message'))];
    }
}
