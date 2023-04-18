<?php

declare(strict_types = 1);

namespace App\Services\Parameter;

use Illuminate\Http\Request;

final class LimitVar extends QueryStringParameter
{
    /** @var int|float */
    private $max;

    /** @var int|float */
    private $min;

    public function __construct(
        string $name = 'limit',
        int|float $default = 10,
        int|float $max = null,
        int|float $min = null,
        ?Request $request = null
    ) 
    {
        parent::__construct($name, $default, $request);
        
        $this->max = $max;
        $this->min = $min;
    }
    
    public function get(): mixed
    {
        $value = parent::get();
        $max = $this->max();
        $min = $this->min();
        
        if ($value > $max) {
            $value = $max;
        }
        
        if ($value < $min) {
            $value = $min;
        }
        
        return $value;
    }
    
    protected function max(): int|float
    {
        $max = $this->max;
        $default = $this->default;
        
        if ($max === null) {
            $max = $default;
        }
        
        if ($default > $max) {
            $max = $default;
        }
        
        return $max;
    }
    
    protected function min(): int|float
    {
        $min = $this->min;
        
        if ($min === null) {
            $min = 1;
        }
        
        return $min;
    }
}
