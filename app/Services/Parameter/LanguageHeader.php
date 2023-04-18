<?php

declare(strict_types = 1);

namespace App\Services\Parameter;

use App\Models\Language;
use Illuminate\Http\Request;

final class LanguageHeader extends HeaderParameter
{
    public function __construct(
        string $name = 'X-language',
        string $default = Language::CODE_PL,
        ?Request $request = null
    ) 
    {
        parent::__construct($name, $default, $request);
    }
    
    public function get(): mixed
    {
        $value = parent::get();
        
        if ( ! in_array($value, Language::codes())) {
            $value = Language::CODE_PL;
        }
        
        return $value;
    }
}
