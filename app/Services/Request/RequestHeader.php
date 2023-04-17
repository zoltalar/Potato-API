<?php

declare(strict_types = 1);

namespace App\Services\Request;

use Illuminate\Http\Request;

class RequestHeader extends BaseRequest
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
        return $this->request->header(
            $this->name(),
            $this->default()
        );
    }
    
    protected function name(): string
    {
        return $this->name;
    }
    
    protected function default(): mixed
    {
        return $this->default;
    }
}
