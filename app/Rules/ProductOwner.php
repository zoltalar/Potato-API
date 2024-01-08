<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Base;
use App\Models\Farm;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductOwner implements Rule
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
            $productable = Farm::find($id);
        } elseif ($type === Product::TYPE_PRODUCTABLE_MARKET) {
            $productable = Market::find($id);
        }
        
        if (isset($productable)) {
            $this->productable = $productable;
        }
    }

    public function passes($attribute, $value): bool
    {
        $productable = $this->productable;
        
        if ($productable !== null) {
            return auth()->id() === $productable->user_id;
        }
        
        return true;
    }

    public function message(): string
    {
        $type = $this->type;
        
        return __("messages.{$type}_product_owner_error");
    }
}
