<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function analytics(Request $request)
    {
        $inventoryId = $request->get('inventory_id');
        $country = $request->header('X-Country', Country::CODE_PL);
        $currency = $request->header('X-currency', Currency::CODE_PLN);

        $prices = Price::query()
            ->selectRaw('inventory.name AS inventory_name, DATE_FORMAT(date, "%m/%Y") AS month_year, AVG(price) AS average_price')
            ->join('inventory', function($join) {
                $join->on('prices.inventory_id', '=', 'inventory.id');
            })
            ->join('currencies', function($join) {
                $join->on('prices.currency_id', '=', 'currencies.id');
            })
            ->whereDate('date', '>', now()->subYear())
            ->when( ! empty($country), function($query) use ($country) {
                return $query->whereHas('productable.addresses.state.country', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->when( ! empty($currency), function($query) use ($currency) {
                return $query->where('currencies.code', $currency);
            })
            ->when( ! empty($inventoryId), function($query) use ($inventoryId) {
                return $query->where('inventory_id', $inventoryId);
            })
            ->groupByRaw('inventory_id, DATE_FORMAT(date, "%m/%Y")')
            ->get();

        return BaseResource::collection($prices);
    }
}
