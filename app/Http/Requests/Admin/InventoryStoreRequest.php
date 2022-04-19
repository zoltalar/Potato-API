<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Models\Base;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;
        $name = $this->name;
        $categoryId = $this->category_id;

        $rules = [
            'name' => ['required', 'string', "max:$length"],
            'category_id' => ['required', 'exists:categories,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ];

        if ( ! empty($name) && ! empty($categoryId)) {
            $unique = Rule::unique('inventory')
                ->where(function($query) use ($name, $categoryId) {
                    return $query
                        ->where('name', $name)
                        ->where('category_id', $categoryId);
                });
            $rules['name'][] = $unique;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.name')),
            'category_id' => mb_strtolower(__('phrases.category')),
            'photo' => mb_strtolower(__('phrases.photo'))
        ];
    }
}
