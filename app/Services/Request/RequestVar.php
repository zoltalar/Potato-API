<?php

declare(strict_types = 1);

namespace App\Services\Request;

use Illuminate\Http\Request;

class RequestVar extends BaseRequest
{
    /** var string */
    protected $name;
    
    /** var mixed */
    protected $default;

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
        $name = $this->name;
        $default = $this->default;
        
        return $this->request->get($name, $default);
    }
}
