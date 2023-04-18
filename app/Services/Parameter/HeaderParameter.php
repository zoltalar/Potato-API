<?php

declare(strict_types = 1);

namespace App\Services\Parameter;

use Illuminate\Http\Request;

class HeaderParameter extends BaseParameter
{
    public function __construct(
        string $name,
        mixed $default = null,
        ?Request $request = null
    )
    {
        parent::__construct($request);
        
        $this->name = $name;
        $this->default = $default;
    }
    
    public function get(): mixed
    {        
        return $this->request->header(
            $this->name(),
            $this->default()
        );
    }
}
