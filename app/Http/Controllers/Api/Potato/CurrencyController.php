<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Currency;
use App\Services\Parameter\LimitVar;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = (new LimitVar())->get();

        $query = Currency::query()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name', 'code', 'symbol', 'number'], $search);
                });
            })
            ->orders('id', 'asc');

        $currencies = ($request->all ? $query->get() : $query->paginate($limit));

        return BaseResource::collection($currencies);
    }
}
