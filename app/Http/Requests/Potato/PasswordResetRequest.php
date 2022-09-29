<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;

class PasswordResetRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return ['email' => ['required', 'email', "max:{$length}"]];
    }

    public function attributes(): array
    {
        return ['email' => mb_strtolower(__('phrases.email'))];
    }
}
