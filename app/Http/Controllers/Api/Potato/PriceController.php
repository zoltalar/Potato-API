<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Price;
use App\Models\Unit;
use App\Services\CurrencyConverter;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function analytics(Request $request)
    {
        $inventoryId = $request->get('inventory_id');
        $productableType = $request->get('productable_type');

        $service = new CurrencyConverter('PLN');

        $prices = Price::query()
            ->select([
                'prices.price',
                'prices.date',
                'inventory.name AS inventory_name',
                'currencies.code AS currency_code'
            ])
            ->join('inventory', 'prices.inventory_id', '=', 'inventory.id')
            ->join('currencies', 'prices.currency_id', '=', 'currencies.id')
            ->whereDate('date', '>=', now()->subYear())
            ->get();

        return BaseResource::collection($prices);
    }
}
