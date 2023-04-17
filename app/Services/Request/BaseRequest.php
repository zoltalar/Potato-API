<?php

declare(strict_types = 1);

namespace App\Services\Request;

use Illuminate\Http\Request;

abstract class BaseRequest
{
    /** @var Request */
    protected $request;
    
    public function __construct(?Request $request = null) 
    {
        if (empty($request)) {
            $request = request();
        }
        
        $this->request = $request;
    }
    
    abstract public function get(): mixed;
}
