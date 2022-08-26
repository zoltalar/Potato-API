<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\OperatingHoursRequest;

class OperatingHourController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function save(OperatingHoursRequest $request, string $type, int $id)
    {

    }
}
