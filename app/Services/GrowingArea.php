<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Language;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

final class GrowingArea
{
    /** @var Collection */
    protected $products;

    /** @var Request */
    protected $request;

    public function setProducts(Collection $products)
    {
        $this->products = $products;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function json(): array
    {
        $json = [];
        $products = $this->products;

        foreach ($products as $product) {
            $category = $this->category($product);
            $inventory = $this->inventory($product);

            if ( ! isset($json[$category])) {
                $json[$category] = [];
            }

            if ( ! in_array($inventory, $json[$category])) {
                $json[$category][] = $inventory;
            }
        }

        return $json;
    }

    protected function category(Product $product): string
    {
        $name = $product->inventory->category->name;
        $language = $this->request->header('X-language', Language::CODE_PL);

        $translation = $product
            ->inventory
            ->category
            ->translations
            ->filter(function($translation) use ($language) {
                return $translation->language->code === $language;
            })
            ->first();

        if ($translation !== null) {
            $name = $translation->name;
        }

        return $name;
    }

    protected function inventory(Product $product): string
    {
        $name = $product->inventory->name;
        $language = $this->request->header('X-language', Language::CODE_PL);

        $translation = $product
            ->inventory
            ->translations
            ->filter(function($translation) use ($language) {
                return $translation->language->code === $language;
            })
            ->first();

        if ($translation !== null) {
            $name = $translation->name;
        }

        return $name;
    }
}
