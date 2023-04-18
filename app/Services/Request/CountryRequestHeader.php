<?php

declare(strict_types = 1);

namespace App\Services\Request;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryRequestHeader extends RequestHeader
{
    public function __construct(
        string $name = 'X-country',
        string $default = Country::CODE_PL,
        ?Request $request = null
    ) 
    {
        parent::__construct($name, $default, $request);
    }
    
    public function get(): mixed
    {
        $value = parent::get();
        
        if ( ! in_array($value, Country::codes())) {
            $value = Country::CODE_PL;
        }
        
        return $value;
    }
}
