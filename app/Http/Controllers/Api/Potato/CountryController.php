<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

        $query = Country::query()
            ->with([
                'states',
                'units'
            ])
            ->active()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name', 'native', 'code'], $search);
                });
            })
            ->orders('name', 'asc');

        $countries = ($request->all ? $query->get() : $query->paginate($limit));

        return BaseResource::collection($countries);
    }
}
