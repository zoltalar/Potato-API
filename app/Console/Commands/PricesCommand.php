<?php

namespace App\Console\Commands;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Console\Command;

class PricesCommand extends Command
{
    protected $signature = 'prices';

    protected $description = 'Generate daily price analytics';

    public function handle()
    {
        $products = Product::query()
            ->whereNotNull('price')
            ->get();

        $date = now()->format('Y-m-d');

        foreach ($products as $product) {
            $price = Price::query()
                ->where('productable_id', $product->productable_id)
                ->where('productable_type', $product->productable_type)
                ->where('inventory_id', $product->inventory_id)
                ->where('date', $date)
                ->first();

            if ($price !== null) {
                $price->fill($product->only($price->getFillable()));
                $price->update();
            } else {
                $price = new Price();
                $price->fill($product->only($price->getFillable()));
                $price->date = $date;
                $price->save();
            }
        }
    }
}
