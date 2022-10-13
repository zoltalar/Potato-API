<?php

declare(strict_types = 1);

namespace App\Services\Response;

use App\Models\Category;
use App\Models\Language;
use App\Models\Inventory;

final class InventoryCategory extends BaseResponse
{
    /** @var array */
    protected $map = [];

    public function json(): array
    {
        $json = [];
        $collection = $this->collection;

        foreach ($collection as $inventory) {
            $category = $this->category($inventory);
            $this->map[$category] = $inventory->category->list_order;
            $json[$category][$this->inventory($inventory)] = $inventory->id;
        }

        uksort($json, function($a, $b) {
            return $this->map[$a] > $this->map[$b];
        });

        foreach ($json as $category => &$data) {
            ksort($data);
        }

        return $json;
    }

    protected function category(Inventory $inventory)
    {
        $name = $inventory->name;
        $language = $this->request->header('X-language', Language::CODE_PL);

        $translation = $inventory
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

    protected function inventory(Inventory $inventory)
    {
        $name = $inventory->name;
        $language = $this->request->header('X-language', Language::CODE_PL);

        $translation = $inventory
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
