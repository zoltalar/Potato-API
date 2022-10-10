<?php

declare(strict_types = 1);

namespace App\Services\Response;

use App\Models\Category;
use App\Models\Language;
use App\Models\Inventory;

final class CategoryInventory extends BaseResponse
{
    public function json(): array
    {
        $json = [];
        $collection = $this->collection;

        foreach ($collection as $category) {

            foreach ($category->inventory as $item) {
                $json[$this->category($category)][$this->inventory($item)] = $item->id;
            }
        }

        foreach ($json as $category => &$data) {
            ksort($data);
        }

        return $json;
    }

    protected function category(Category $category): string
    {
        $name = $category->name;
        $language = $this->request->header('X-language', Language::CODE_PL);

        $translation = $category
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

    protected function inventory(Inventory $item): string
    {
        $name = $item->name;
        $language = $this->request->header('X-language', Language::CODE_PL);

        $translation = $item
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
