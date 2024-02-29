<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Rules\ReviewToSelf;
use App\Rules\ReviewUnique;

class ReviewStoreRequest extends BaseRequest
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
            'rating' => [
                'required',
                'numeric',
                'min:0',
                'max:5',
                new ReviewToSelf($type, $id),
                new ReviewUnique($type, $id)
            ],
            'title' => ['nullable', "max:{$length}"],
            'content' => ['required', 'max:2000']
        ];
    }

    public function attributes(): array
    {
        return [
            'rating' => mb_strtolower(__('phrases.rating')),
            'title' => mb_strtolower(__('phrases.title')),
            'content' => mb_strtolower(__('phrases.review'))
        ];
    }
}
