<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Models\Base;
use Illuminate\Foundation\Http\FormRequest;

class InventoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'name' => ['required', 'string', "max:$length"],
            'category_id' => ['required', 'exists:categories,id']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.name')),
            'category_id' => mb_strtolower(__('phrases.category'))
        ];
    }
}
