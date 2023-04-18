<?php

declare(strict_types = 1);

namespace App\Services\Request;

use Illuminate\Http\Request;

class RequestVar extends BaseRequest
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
        return $this->request->get(
            $this->name(),
            $this->default()
        );
    }
}
