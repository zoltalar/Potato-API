<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\ProductAvailabilitySeasons;
use App\Rules\ProductOwner;
use App\Rules\ProductUnique;

class ProductStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->route('type');
        $id = $this->route('id');
        $product = $this->all();
        
        return [
            'inventory_id' => [
                'required', 
                'exists:inventory,id', 
                new ProductUnique($type, $id),
                new ProductOwner($type, $id)
            ],
            'seasons' => [new ProductAvailabilitySeasons($product)],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'amount_unit' => ['nullable', 'required_with:amount'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'currency_id' => ['nullable', 'exists:currencies,id', 'required_with:price'],
            'price_unit' => ['nullable', 'required_with:price']
        ];
    }

    public function attributes(): array
    {
        return [
            'inventory_id' => mb_strtolower(__('phrases.product')),
            'amount' => mb_strtolower(__('phrases.amount')),
            'amount_unit' => mb_strtolower(__('phrases.unit')),
            'price' => mb_strtolower(__('phrases.price')),
            'currency_id' => mb_strtolower(__('phrases.currency')),
            'price_unit' => mb_strtolower(__('phrases.unit'))
        ];
    }
    
    protected function prepareForValidation()
    {
        $product = $this->all();
        $product['seasons'] = [];
        
        $this->merge($product);
    }
}
