<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Models\Base;
use Illuminate\Foundation\Http\FormRequest;

class AdminStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', "max:{$length}", 'unique:admins,email'],
            'phone' => ['nullable', 'max:20'],
            'password' => ['required', 'string', 'min:7']
        ];
    }
}
