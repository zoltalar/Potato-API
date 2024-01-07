<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Base;
use App\Models\Farm;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductUnique implements Rule
{
    /** @var string */
    protected $type;
    
    /** @var int */
    protected $id;
    
    /** @var Base */
    protected $productable;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
        
        $this->initialize();
    }
    
    protected function initialize(): void
    {
        $type = $this->type;
        $id = $this->id;
        
        if ($type === Product::TYPE_PRODUCTABLE_FARM) {
            $productable = Farm::query()
                ->with(['products'])
                ->find($id);
        } elseif ($type === Product::TYPE_PRODUCTABLE_MARKET) {
            $productable = Market::query()
                ->with(['products'])
                ->find($id);
        }
        
        if (isset($productable)) {
            $this->productable = $productable;
        }
    }

    public function passes($attribute, $value): bool
    {
        $productable = $this->productable;
        
        if ($productable !== null) {
            $product = $productable
                ->products
                ->filter(function($product) use ($value) {
                    return $product->inventory_id === (int) $value;
                })
                ->first();
                
            return $product === null;
        }
        
        return true;
    }

    public function message(): string
    {
        return __('messages.product_unique_error');
    }
}
