<?php

declare(strict_types = 1);

namespace App\Services\Response;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

abstract class BaseResponse
{
    /** @var Collection */
    protected $collection;

    /** @var Request */
    protected $request;

    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    abstract public function json(): array;
}
