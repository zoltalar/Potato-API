<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;

class MarketSocialMediaUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facebook' => ['nullable', 'max:100'],
            'twitter' => ['nullable', 'max:100'],
            'pinterest' => ['nullable', 'max:100'],
            'instagram' => ['nullable', 'max:100']
        ];
    }

    public function attributes(): array
    {
        return [
            'facebook' => mb_strtolower(__('phrases.facebook')),
            'twitter' => mb_strtolower(__('phrases.twitter')),
            'pinterest' => mb_strtolower(__('phrases.pinterest')),
            'instagram' => mb_strtolower(__('phrases.instagram'))
        ];
    }
}
