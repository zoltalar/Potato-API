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
            $rules["products.{$i}.amount"] = ['nullable', 'numeric', 'max:9999999'];
            $rules["products.{$i}.amount_unit"] = ['nullable'];
            $rules["products.{$i}.price"] = ['nullable', 'numeric', 'max:9999999'];
            $rules["products.{$i}.currency_id"] = ['nullable', 'exists:currencies,id'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        $products = $this->get('products', []);

        foreach ($products as $i => $product) {
            $attributes["products.{$i}.amount"] = mb_strtolower(__('phrases.amount'));
            $attributes["products.{$i}.amount_unit"] = mb_strtolower(__('phrases.unit'));
            $attributes["products.{$i}.price"] = mb_strtolower(__('phrases.price'));
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
