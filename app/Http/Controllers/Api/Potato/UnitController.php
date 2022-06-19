<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function meta()
    {
        $meta = [
            'types' => Unit::types(),
            'systems' => Unit::systems()
        ];

        return response()->json($meta);
    }
}
