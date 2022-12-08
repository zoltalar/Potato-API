<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\CommentToSelf;

class CommentStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->route('type');
        $id = $this->route('id');

        return [
            'content' => ['required', 'max:1000', new CommentToSelf($type, $id)]
        ];
    }

    public function attributes(): array
    {
        return ['content' => mb_strtolower(__('phrases.comment'))];
    }
}
