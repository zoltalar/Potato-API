<?php

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\ProductAvailabilitySeasons;

class ProductsRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];
        $products = $this->get('products', []);

        foreach ($products as $i => $product) {
            $rules["products.{$i}.seasons"] = [new ProductAvailabilitySeasons($product)];
            $rules["products.{$i}.amount"] = ['nullable', 'numeric'];
            $rules["products.{$i}.unit"] = ['nullable'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        $products = $this->get('products', []);

        foreach ($products as $i => $product) {
            $attributes["products.{$i}.amount"] = mb_strtolower(__('phrases.amount'));
            $attributes["products.{$i}.unit"] = mb_strtolower(__('phrases.unit'));
        }

        return $attributes;
    }

    protected function prepareForValidation()
    {
        if ($this->has('products')) {
            $products = $this->get('products', []);

            foreach ($products as & $product) {
                $product['seasons'] = [];
            }

            $this->merge(['products' => $products]);
        }
    }
}
