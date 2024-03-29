<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        $rules = [
            'name' => ['required', 'string', "max:{$length}"],
            'type' => ['required', 'in:' . implode(',', array_keys(Category::types()))],
            'list_order' => ['nullable', 'numeric', 'max:9999']
        ];

        $name = $this->name;
        $type = $this->type;

        if ( ! empty($name) && ! empty($type)) {
            $unique = Rule::unique('categories')
                ->where(function($query) use ($name, $type) {
                    return $query
                        ->where('name', $name)
                        ->where('type', $type);
                });
            $rules['name'][] = $unique;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.name')),
            'type' => mb_strtolower(__('phrases.type')),
            'list_order' => mb_strtolower(__('phrases.list_order'))
        ];
    }
}
