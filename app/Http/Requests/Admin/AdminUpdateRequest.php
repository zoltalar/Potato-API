<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\Base;

class AdminUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;
        $admin = $this->route('admin');
        $id = $admin->id ?? null;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', "max:{$length}", "unique:admins,email,{$id}"],
            'phone' => ['nullable', 'max:20'],
            'password' => ['nullable', 'string', 'min:7']
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => mb_strtolower(__('phrases.first_name')),
            'last_name' => mb_strtolower(__('phrases.last_name')),
            'email' => mb_strtolower(__('phrases.email')),
            'phone' => mb_strtolower(__('phrases.phone')),
            'password' => mb_strtolower(__('phrases.password'))
        ];
    }
}
