<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Inventory;
use App\Models\Language;
use App\Models\Translation;
use Arr;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /** @var array */
    protected $cache = [];

    public function run()
    {
        foreach ($this->inventory() as $inventory) {
            $countries = $inventory['countries'] ?? [];
            $translations = $inventory['translations'] ?? [];

            unset($inventory['translations']);
            unset($inventory['countries']);

            if ( ! isset($this->cache['categories'][$inventory['category']])) {
                $category = Category::where('name', $inventory['category'])->first();

                if ($category !== null) {
                    $this->cache['categories'][$inventory['category']] = $category->id;
                }
            }

            if (isset($this->cache['categories'][$inventory['category']])) {
                $inventory['category_id'] = $this->cache['categories'][$inventory['category']];
                unset($inventory['category']);

                $inventory = Inventory::firstOrCreate(
                    Arr::only($inventory, ['name', 'category_id']),
                    Arr::except($inventory, ['name', 'category_id'])
                );

                if ($inventory !== null) {

                    // Add country availability
                    foreach ($countries as $countryName) {
                        if ( ! isset($this->cache['countries'][$countryName])) {
                            $country = Country::where('name', $countryName)->first();

                            if ($country !== null) {
                                $this->cache['countries'][$countryName] = $country;
                            }
                        }

                        if (isset($this->cache['countries'][$countryName])) {
                            $country = $this->cache['countries'][$countryName];
                            $inventory->countries()->attach($country->id);
                        }
                    }

                    // Add translations
                    $translations = ['English' => $inventory->name] + $translations;

                    foreach ($translations as $languageName => $inventoryName) {
                        if ( ! isset($this->cache['languages'][$languageName])) {
                            $language = Language::where('name', $languageName)->first();

                            if ($language !== null) {
                                $this->cache['languages'][$languageName] = $language;
                            }
                        }

                        if (isset($this->cache['languages'][$languageName])) {
                            $language = $this->cache['languages'][$languageName];

                            if ($inventory->translation($language) === null) {
                                $attributes = ['name' => $inventoryName, 'language_id' => $language->id];
                                $inventory->translations()->save(new Translation($attributes));
                            }
                        }
                    }
                }
            }
        }
    }

    protected function inventory(): array
    {
        return array_merge(
            $this->fruits(),
            $this->vegetables(),
            $this->dairyAndEggs()
        );
    }

    protected function fruits(): array
    {
        return [
            [
                'name' => 'Apples',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Jabłka'
                ]
            ],
            [
                'name' => 'Apricots',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Morele'
                ]
            ],
            [
                'name' => 'Avocados',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Awokado'
                ]
            ],
            [
                'name' => 'Bananas',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Banany'
                ]
            ],
            [
                'name' => 'Blackberries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Jeżyny'
                ]
            ],
            [
                'name' => 'Blackcurrants',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Porzeczka Czarna'
                ]
            ],
            [
                'name' => 'Blueberries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Borówka Amerykańska'
                ]
            ],
            [
                'name' => 'Cherries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Czereśnie'
                ]
            ],
            [
                'name' => 'Cranberries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Żurawina'
                ]
            ],
            [
                'name' => 'Dates',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Daktyle'
                ]
            ],
            [
                'name' => 'Figs',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Figi'
                ]
            ],
            [
                'name' => 'Gooseberries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Agrest'
                ]
            ],
            [
                'name' => 'Grapefruits',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Grejpfrut'
                ]
            ],
            [
                'name' => 'Lemons',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Cytryny'
                ]
            ],
            [
                'name' => 'Limes',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Limonki'
                ]
            ],
            [
                'name' => 'Mandarins',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Mandarynki'
                ]
            ],
            [
                'name' => 'Oranges',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Pomarańcze'
                ]
            ],
            [
                'name' => 'Peaches',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Brzoskwinie'
                ]
            ],
            [
                'name' => 'Pears',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Gruszki'
                ]
            ],
            [
                'name' => 'Plums',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Śliwka'
                ]
            ],
            [
                'name' => 'Pomegranates',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [],
                'translations' => [
                    'Polish' => 'Granaty'
                ]
            ],
            [
                'name' => 'Quinces',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Pigwy'
                ]
            ],
            [
                'name' => 'Raspberry',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Malina'
                ]
            ],
            [
                'name' => 'Redcurrant',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Porzeczka Czerwona'
                ]
            ],
            [
                'name' => 'Sour Cherries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Wiśnia'
                ]
            ],
            [
                'name' => 'Strawberries',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Truskawka'
                ]
            ]
        ];
    }

    protected function vegetables(): array
    {
        return [
            [
                'name' => 'Arrowroot',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'United States'
                ]
            ],
            [
                'name' => 'Artichoke',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Karczoch'
                ]
            ],
            [
                'name' => 'Arugula',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Rukola'
                ]
            ],
            [
                'name' => 'Asparagus',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Szparag'
                ]
            ],
            [
                'name' => 'Beans, Green',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Fasola'
                ]
            ],
            [
                'name' => 'Beets',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Buraki'
                ]
            ],
            [
                'name' => 'Bok Choy',
                'category' => 'Vegetables',
                'system' => 1
            ],
            [
                'name' => 'Broad beans',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Bób'
                ]
            ],
            [
                'name' => 'Broccoli',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Brokuły'
                ]
            ],
            [
                'name' => 'Brussel Sprouts',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Brukselka'
                ]
            ],
            [
                'name' => 'Cabbage, Green',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Zielona Kapusta'
                ]
            ],
            [
                'name' => 'Cabbage, Red',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Czerwona Kapusta'
                ]
            ],
            [
                'name' => 'Carrot',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Marchewka'
                ]
            ],
            [
                'name' => 'Cassava',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Maniok'
                ]
            ],
            [
                'name' => 'Cauliflower',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Kalafior'
                ]
            ],
            [
                'name' => 'Celery',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Seler'
                ]
            ],
            [
                'name' => 'Chickpeas',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Ciecierzyca'
                ]
            ],
            [
                'name' => 'Corn',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Kukurydza'
                ]
            ],
            [
                'name' => 'Crookneck',
                'category' => 'Vegetables',
                'system' => 1
            ],
            [
                'name' => 'Cucumber',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Ogórek'
                ]
            ],
            [
                'name' => 'Daikon',
                'category' => 'Vegetables',
                'system' => 1
            ],
            [
                'name' => 'Eggplant',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Bakłażan'
                ]
            ],
            [
                'name' => 'Fennel',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Koper'
                ]
            ],
            [
                'name' => 'Ginger Root',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Imbir'
                ]
            ],
            [
                'name' => 'Horseradish',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Chrzan'
                ]
            ],
            [
                'name' => 'Jicama',
                'category' => 'Vegetables',
                'system' => 1
            ],
            [
                'name' => 'Kale',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Jarmuż'
                ]
            ],
            [
                'name' => 'Kohlrabi',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Kalarepa'
                ]
            ],
            [
                'name' => 'Leeks',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Pory'
                ]
            ],
            [
                'name' => 'Lettuce',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Sałata'
                ]
            ],
            [
                'name' => 'Mushrooms',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Pieczarki'
                ]
            ],
            [
                'name' => 'Mustard Greens',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Gorczyca'
                ]
            ],
            [
                'name' => 'Okra',
                'category' => 'Vegetables',
                'system' => null,
                'translations' => [
                    'Polish' => 'Róża Chińska'
                ]
            ],
            [
                'name' => 'Onion, Red',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Czerwona Cebula'
                ]
            ],
            [
                'name' => 'Onion',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Cebula'
                ]
            ],
            [
                'name' => 'Parsnip',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Pasternak'
                ]
            ],
            [
                'name' => 'Peas, Green',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Zielony Groszek'
                ]
            ],
            [
                'name' => 'Pepper, Green',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Zielona Papryka'
                ]
            ],
            [
                'name' => 'Pepper, Sweet Red',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Czerwona Papryka'
                ]
            ],
            [
                'name' => 'Potato, Red',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Czerwone Ziemniaki'
                ]
            ],
            [
                'name' => 'Potato, White',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Białe Ziemniaki'
                ]
            ],
            [
                'name' => 'Potato, Yellow',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Żółte Ziemniaki'
                ]
            ],
            [
                'name' => 'Pumpkin',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Dynia'
                ]
            ],
            [
                'name' => 'Radishes',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Rzodkiewki'
                ]
            ],
            [
                'name' => 'Rutabaga',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Brukiew'
                ]
            ],
            [
                'name' => 'Shallots',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Szalotki'
                ]
            ],
            [
                'name' => 'Snow Peas',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Śnieżny Groszek'
                ]
            ],
            [
                'name' => 'Sorrel (Dock)',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Szczaw'
                ]
            ],
            [
                'name' => 'Spinach',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Szpinak'
                ]
            ],
            [
                'name' => 'Sugar Snap Peas',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Groszek Cukrowy'
                ]
            ],
            [
                'name' => 'Sweet Potato',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Słodki Ziemniak'
                ]
            ],
            [
                'name' => 'Swiss Chard',
                'category' => 'Vegetables',
                'system' => 1,
                'translations' => [
                    'Polish' => 'Boćwina Szwajcarska'
                ]
            ],
            [
                'name' => 'Tomatos',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Pomidory'
                ]
            ],
            [
                'name' => 'Turnip',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Rzepa'
                ]
            ],
            [
                'name' => 'Watercress',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Rukiew Wodna'
                ]
            ],
            [
                'name' => 'Zucchini',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Cukinia'
                ]
            ]
        ];
    }

    protected function dairyAndEggs(): array
    {
        return [
            [
                'name' => 'Butter',
                'category' => 'Dairy and Eggs',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Masło'
                ]
            ],
            [
                'name' => 'Cheese',
                'category' => 'Dairy and Eggs',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Ser'
                ]
            ],
            [
                'name' => 'Eggs',
                'category' => 'Dairy and Eggs',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Jaja'
                ]
            ],
            [
                'name' => 'Milk',
                'category' => 'Dairy and Eggs',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Mleko'
                ]
            ],
            [
                'name' => 'Yogurt',
                'category' => 'Dairy and Eggs',
                'system' => 1,
                'countries' => [
                    'Poland',
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Jogurt'
                ]
            ]
        ];
    }
}
