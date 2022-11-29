<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\Base;

class MarketUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'name' => ['required', 'string', "max:{$length}"],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'max:20'],
            'fax' => ['nullable', 'max:20'],
            'email' => ['required', 'email', "max:{$length}"],
            'website' => ['nullable', "max:{$length}"],
            'description' => ['nullable', 'max:5000'],
            'facebook' => ['nullable', 'max:100'],
            'twitter' => ['nullable', 'max:100'],
            'pinterest' => ['nullable', 'max:100'],
            'instagram' => ['nullable', 'max:100']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.name')),
            'first_name' => mb_strtolower(__('phrases.first_name')),
            'last_name' => mb_strtolower(__('phrases.last_name')),
            'phone' => mb_strtolower(__('phrases.phone')),
            'fax' => mb_strtolower(__('phrases.fax')),
            'email' => mb_strtolower(__('phrases.email')),
            'website' => mb_strtolower(__('phrases.website')),
            'description' => mb_strtolower(__('phrases.description')),
            'facebook' => mb_strtolower(__('phrases.facebook')),
            'twitter' => mb_strtolower(__('phrases.twitter')),
            'pinterest' => mb_strtolower(__('phrases.pinterest')),
            'instagram' => mb_strtolower(__('phrases.instagram'))
        ];
    }
}
