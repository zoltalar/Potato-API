<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;
use App\Rules\MessageToSelf;

class MessageReplyRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['content' => ['required', 'max:1000']];
    }

    public function attributes(): array
    {
        return ['content' => mb_strtolower(__('phrases.message'))];
    }
}
