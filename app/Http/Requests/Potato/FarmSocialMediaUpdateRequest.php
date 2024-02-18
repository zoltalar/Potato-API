<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\FarmOwner;

class FarmSocialMediaUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        
        return [
            'facebook' => ['nullable', 'max:100', new FarmOwner($id)],
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
