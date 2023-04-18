<?php

declare(strict_types = 1);

namespace App\Services\Request;

use Illuminate\Http\Request;

abstract class BaseRequest
{
    /** @var Request */
    protected $request;
    
    /** var string */
    protected $name;
    
    /** var mixed */
    protected $default;
    
    public function __construct(?Request $request = null) 
    {
        if (empty($request)) {
            $request = request();
        }
        
        $this->request = $request;
    }
    
    protected function name(): string
    {
        return $this->name;
    }
    
    protected function default(): mixed
    {
        return $this->default;
    }
    
    abstract public function get(): mixed;
}
