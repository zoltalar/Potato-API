<?php

declare(strict_types = 1);

namespace App\Services\Response;

use App\Models\Language;
use App\Models\Product;

final class GrowingArea extends BaseResponse
{
    public function json(): array
    {
        $json = [];
        $collection = $this->collection;

        foreach ($collection as $product) {
            $category = $this->category($product);
            $inventory = $this->inventory($product);

            $json[$category][$inventory] = $product->inventory_id;
        }

        foreach ($json as $category => &$data) {
            ksort($data);
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
