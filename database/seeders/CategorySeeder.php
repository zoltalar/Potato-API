<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Translation;
use Arr;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /** @var array */
    protected $cache = [];

    public function run()
    {
        foreach ($this->categories() as $category) {
            $translations = $category['translations'] ?? [];
            unset($category['translations']);

            $category = Category::firstOrCreate(
                Arr::only($category, ['name', 'type']),
                Arr::except($category, ['name', 'type'])
            );

            // Add translations
            if ($category !== null) {
                $translations = [Language::NAME_ENGLISH => $category->name] + $translations;

                foreach ($translations as $languageName => $categoryName) {
                    if ( ! isset($this->cache['languages'][$languageName])) {
                        $language = Language::where('name', $languageName)->first();

                        if ($language !== null) {
                            $this->cache['languages'][$languageName] = $language;
                        }
                    }

                    if (isset($this->cache['languages'][$languageName])) {
                        $language = $this->cache['languages'][$languageName];

                        if ($category->translation($language) === null) {
                            $attributes = ['name' => $categoryName, 'language_id' => $language->id];
                            $category->translations()->save(new Translation($attributes));
                        }
                    }
                }
            }
        }
    }

    protected function categories(): array
    {
        return [
            [
                'name' => 'Coffee and Teas',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 5,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Kawa i Herbaty'
                ]
            ],
            [
                'name' => 'Dairy and Eggs',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 3,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Nabiał i Jaja'
                ]
            ],
            [
                'name' => 'Fruits',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 1,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Owoce'
                ]
            ],
            [
                'name' => 'Honey',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 5,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Miód'
                ]
            ],
            [
                'name' => 'Meats',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 8,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Mięsa'
                ]
            ],
            [
                'name' => 'Nuts and Seeds',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 4,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Orzechy i Nasiona'
                ]
            ],
            [
                'name' => 'Preserves',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 7,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Przetwory'
                ]
            ],
            [
                'name' => 'Vegetables',
                'type' => Category::TYPE_INVENTORY,
                'list_order' => 2,
                'system' => 1,
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Warzywa'
                ]
            ]
        ];
    }
}
