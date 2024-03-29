<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Inventory;
use App\Models\Language;
use App\Models\Substance;
use App\Models\Translation;
use App\Models\Unit;
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
            $substances = $inventory['substances'] ?? [];
            $translations = $inventory['translations'] ?? [];

            unset($inventory['translations']);
            unset($inventory['substances']);
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

                            if ( ! $inventory->countries()->get()->contains($country->id)) {
                                $inventory->countries()->attach($country->id);
                            }
                        }
                    }
                    
                    // Add substances
                    foreach ($substances as $substanceAttributes) {
                        $substanceName = $substanceAttributes['name'];
                        
                        if ( ! isset($this->cache['substances'][$substanceName])) {
                            $substance = Substance::where('name', $substanceName)->first();
                            
                            if ($substance !== null) {
                                $this->cache['substances'][$substanceName] = $substance;
                            }
                        }
                        
                        if (isset($this->cache['substances'][$substanceName])) {
                            $substance = $this->cache['substances'][$substanceName];
                            
                            if ($inventory->substance($substance) === null) {
                                $attributes = [
                                    'value' => $substanceAttributes['value'],
                                    'value_unit' => $substanceAttributes['value_unit'] ?? null
                                ];
                                
                                $inventory->substances()->attach($substance, $attributes);
                            }
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
            $this->dairyAndEggs(),
            $this->nutsAndSeeds(),
            $this->coffeeAndTeas(),
            $this->honey(),
            $this->grains(),
            $this->processedFood(),
            $this->meats()
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 52
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.8,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sugar',
                        'value' => 10.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fiber',
                        'value' => 2.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 107,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 4.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Magnesium',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ]
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 48
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.39,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 13,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 259,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 96,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 160
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 8.53,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 14.66,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.55,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 485,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 7,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 89
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.09,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 22.84,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.33,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.26,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 358,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 8.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 43
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.39,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 9.61,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.49,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 29,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.62,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 162,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 11,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 21,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 63
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 15.38,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.41,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 55,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.54,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 322,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 181,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 57
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.74,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 14.49,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.33,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.28,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 77,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 9.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 74
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.24,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 18.73,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.23,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.42,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 260,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 8.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 50
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],                    
                    [
                        'name' => 'Potassium',
                        'value' => 150,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 14,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 282
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.45,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 75.03,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.39,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 39,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.02,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 656,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 74
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.75,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 19.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 35,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.37,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 232,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 7,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 44
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.88,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 10.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.58,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.31,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 198,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 27.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 32
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.63,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 8.08,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.09,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 139,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 46,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 34.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Grejpfrut'
                ]
            ],
            [
                'name' => 'Grapes',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 69
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.72,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 18.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.16,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.36,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 191,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 10.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Winogrono'
                ]
            ],
            [
                'name' => 'Lemons',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 29
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 9.32,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 26,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 138,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 53,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 30
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 10.54,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 102,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 29.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 48
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 10.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 238,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 47
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.94,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11.75,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 40,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 181,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 11,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 53.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 39
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.91,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 9.54,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.25,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 190,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 6.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 58
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.38,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 15.46,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.17,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 119,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 4.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 46
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11.42,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.28,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.17,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 157,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 17,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 9.5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 68
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.95,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 17.17,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 259,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 6.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Granaty'
                ]
            ],
            [
                'name' => 'Quinces',
                'category' => 'Fruits',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 57
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 15.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 11,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 197,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 52
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11.94,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.65,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.69,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 151,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 26.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 56
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.8,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 275,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 41,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 50
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 12.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.32,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 173,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 64,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 32
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.67,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.68,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.42,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 153,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 58.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 65
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 4.24,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.39,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 26,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.22,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 454,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 1.9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Maranta'
                ]
            ],
            [
                'name' => 'Artichoke',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 47
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 94,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 21,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 286,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 11.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 25
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.58,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.65,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.66,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 27,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 160,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.46,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 369,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 119,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 20
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.88,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.14,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 202,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 38,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 5.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 35
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 44,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 146,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 43
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.61,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 9.56,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.17,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 78,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 325,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 4.9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Buraki'
                ]
            ],
            [
                'name' => 'Bok Choy',
                'category' => 'Vegetables',
                'system' => 1,
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 13
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 2.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 65,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 105,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 252,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 223,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 45,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
            ],
            [
                'name' => 'Broad beans',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 72
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 5.6,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 50,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 22,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 250,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 18,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 34
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.82,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 6.64,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.37,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 47,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.73,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 316,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 31,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 89.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 43
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.38,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 8.95,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 42,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 389,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 38,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 85,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 24
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.44,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 5.58,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 18,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 47,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.59,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 246,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 9,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 32.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 31
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.43,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.37,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.16,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 27,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 45,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 243,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 56,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 57,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 41
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.93,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 9.58,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.24,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 69,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 320,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 841,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 5.9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 160
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.36,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 38.06,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.28,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 14,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.27,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 271,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 20.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 25
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.98,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 5.03,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 30,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 22,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.44,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 303,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 46.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 14
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.69,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 2.97,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.17,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 80,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 40,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 260,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 22,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 3.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 364
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 19,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 61,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 30.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 177,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 86
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.22,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 19.02,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 1.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.52,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 270,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 6.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 15
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.65,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.63,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.11,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.28,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 147,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 24
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.01,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 5.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.19,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.24,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 230,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 31
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.24,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.29,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 52,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 49,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.73,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 414,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 7,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 80
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.82,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 17.77,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.75,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 13,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 415,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 48
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 11.29,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.69,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 314,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 56,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.42,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 246,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 24.9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 50
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 10.01,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 43,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 135,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 447,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 769,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 120,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 27
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 6.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 20,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 350,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 62,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 61
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 14.15,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 20,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 59,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 180,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 83,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 14
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 2.97,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.14,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 18,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.41,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 141,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 22
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.09,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.28,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.34,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 318,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 26
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 4.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 103,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.46,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 354,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 525,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 70,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 42
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.92,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 10.11,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.08,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 22,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.19,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 144,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 6.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 42
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.92,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 10.11,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.08,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 22,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.19,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 144,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 6.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 75
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 17.99,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 36,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.59,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 375,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 17,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 81
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 5.42,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 14.46,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.47,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 244,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 38,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 40,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 20
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.86,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 4.64,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.17,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.34,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 175,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 18,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 80.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 26
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.99,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 6.03,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.43,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 211,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 157,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 190,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 72
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.89,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 15.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.14,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.73,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 455,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 19.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 94
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 21.08,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.15,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.64,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 544,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 12.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 74
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.02,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 17.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 418,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 26
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 6.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 21,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 340,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 369,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 16
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.68,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 39,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.34,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 233,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 14.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 36
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 8.13,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 20,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 47,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.52,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 337,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 72
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 16.8,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 37,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 334,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 60,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 42
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.8,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.55,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 43,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.08,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 200,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 54,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 60,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 22
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 44,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 390,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 200,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 48,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 23
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.86,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.63,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.39,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 79,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 99,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.71,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 558,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 469,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 28.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 42
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.8,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.55,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 43,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.08,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 200,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 54,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 60,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 86
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.57,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 20.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.05,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 55,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 30,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.61,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 337,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 709,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Słodki Ziemniak'
                ]
            ],
            [
                'name' => 'Swiss Chard',
                'category' => 'Vegetables',
                'system' => 1,
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 19
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.8,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.74,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 213,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 51,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 379,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 306,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 30,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Boćwina Szwajcarska'
                ]
            ],
            [
                'name' => 'Tomatos',
                'category' => 'Vegetables',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 18
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.88,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.92,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.27,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 237,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 42,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 12.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 28
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 6.43,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 67,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 30,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 191,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 42,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 21,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 11
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 1.29,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.1,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 41,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 120,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 330,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 235,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 43,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 16
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 1.21,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.35,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.35,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 262,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 17,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 717
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.85,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 0.06,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 81.11,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 11,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.02,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 684,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ]
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 350
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 22.21,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 4.71,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 26.91,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 83,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 955,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 651,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.51,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 187,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 210,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ]
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 147
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 12.58,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 0.77,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 9.94,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 423,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 140,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 53,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.83,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 134,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 140,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ]
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 60
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.22,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 4.52,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 3.25,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 40,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 113,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.03,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 143,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 28,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ]
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
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 63
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 5.25,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 7.04,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 1.55,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 70,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 183,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.08,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 234,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 14,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Jogurt'
                ]
            ]
        ];
    }

    protected function nutsAndSeeds(): array
    {
        return [
            [
                'name' => 'Almonds',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 578
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 21.26,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 19.74,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 50.64,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 248,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 4.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 728,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Migdały'
                ]
            ],
            [
                'name' => 'Cacao',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 263
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 19,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 19.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 20.6,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 50,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Kakao'
                ]
            ],
            [
                'name' => 'Chestnuts',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 245
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.17,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 52.96,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 2.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 29,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.91,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 592,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 26,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Kasztany'
                ]
            ],
            [
                'name' => 'Flax',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 534
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 18.29,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 28.88,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 42.16,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 30,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 255,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 5.73,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 813,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Len'
                ]
            ],
            [
                'name' => 'Hazelnuts',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 628
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 14.95,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 16.7,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 60.75,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 114,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 4.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 680,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 6.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Orzechy Laskowe'
                ]
            ],
            [
                'name' => 'Macadamias',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 718
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 7.91,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.82,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 75.77,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 85,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 3.69,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 368,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 1.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Makadamia'
                ]
            ],
            [
                'name' => 'Peanuts',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 599
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 28.03,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 15.26,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 52.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 61,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.52,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 726,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Orzeszki Ziemne'
                ]
            ],
            [
                'name' => 'Pecans',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 691
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 9.17,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.86,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 71.97,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 70,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.53,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 410,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 1.1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Orzechy Pekan'
                ]
            ],
            [
                'name' => 'Pine Nuts',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 673
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 13.69,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.08,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 68.37,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 16,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 5.53,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 597,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Orzeszki Piniowe'
                ]
            ],
            [
                'name' => 'Pistachios',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 557
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 20.61,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 27.97,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 44.44,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 107,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 4.15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 1025,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 28,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Pistacje'
                ]
            ],
            [
                'name' => 'Sunflower Seeds',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 570
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 22.78,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 18.76,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 49.57,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 116,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 6.77,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 689,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 3,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 1.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Ziarna Słonecznika'
                ]
            ],
            [
                'name' => 'Walnuts',
                'category' => 'Nuts and Seeds',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 654
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 15.23,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 13.71,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 65.21,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 98,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.91,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 441,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 1.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Orzechy Włoskie'
                ]
            ]
        ];
    }

    protected function coffeeAndTeas(): array
    {
        return [
            [
                'name' => 'Coffee',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 1
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.12,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 0.04,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.02,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.02,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 47,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Kawa'
                ]
            ],
            [
                'name' => 'Black Tea',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 1
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.01,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Czarna Herbata'
                ]
            ],
            [
                'name' => 'Green Tea',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 1
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 0.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.08,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 9,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Zielona Herbata'
                ]
            ],
            [
                'name' => 'White Tea',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [],
                'translations' => [
                    'Polish' => 'Biała Herbata'
                ]
            ],
            [
                'name' => 'Yellow Tea',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [],
                'translations' => [
                    'Polish' => 'Żółta Herbata'
                ]
            ]
        ];
    }

    protected function honey(): array
    {
        return [
            [
                'name' => 'Honeydew Honey',
                'category' => 'Honey',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Miód Spadziowy'
                ]
            ],
            [
                'name' => 'Wildflower Honey',
                'category' => 'Honey',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Miód Wielokwiatowy'
                ]
            ],
            [
                'name' => 'Linden Honey',
                'category' => 'Honey',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Miód Lipowy'
                ]
            ],
            [
                'name' => 'Acacia Honey',
                'category' => 'Honey',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Miód Akacjowy'
                ]
            ],
            [
                'name' => 'Buckwheat Honey',
                'category' => 'Honey',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Miód Gryczany'
                ]
            ],
            [
                'name' => 'Rapeseed Honey',
                'category' => 'Honey',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND
                ],
                'translations' => [
                    'Polish' => 'Miód Rzepakowy'
                ]
            ],
        ];
    }

    protected function grains(): array
    {
        return [
            [
                'name' => 'Barley',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 354
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 12.48,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 73.48,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 2.3,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 3.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 452,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ]
                ],
                'translations' => [
                    'Polish' => 'Jęczmień'
                ]
            ],
            [
                'name' => 'Buckwheat',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 343
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 13.25,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 71.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 3.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 18,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 460,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Gryka'
                ]
            ],
            [
                'name' => 'Corn',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 86
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 3.22,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 19.02,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 1.18,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.52,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 270,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Kukurydza'
                ]
            ],
            [
                'name' => 'Millet',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 378
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 11.02,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 72.85,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 4.22,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 3.01,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 195,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Proso'
                ]
            ],
            [
                'name' => 'Oats',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 389
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 16.89,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 66.27,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 6.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 54,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 4.72,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 429,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Owies'
                ]
            ],
            [
                'name' => 'Popcorn',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'translations' => []
            ],
            [
                'name' => 'Rice',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 129
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 2.66,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 27.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.28,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.19,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 35,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Ryż'
                ]
            ],
            [
                'name' => 'Rye',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 335
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 14.76,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 69.76,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 2.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 33,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.67,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 264,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 1,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Żyto'
                ]
            ],
            [
                'name' => 'Soy Beans',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 81
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 8.47,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 6.53,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 4.45,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 59,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.31,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 355,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 8.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Fasola Sojowa'
                ]
            ],
            [
                'name' => 'Spelt',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 338
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 70,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 2.4,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.7,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 143,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Orkisz'
                ]
            ],
            [
                'name' => 'Wheat',
                'category' => 'Grains',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 198
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 7.49,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 42.53,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 1.27,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 28,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.14,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 169,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 2.6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Pszenica'
                ]
            ]
        ];
    }

    protected function processedFood(): array
    {
        return [
            [
                'name' => 'Baked Goods',
                'category' => 'Processed Food',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],                
                'translations' => [
                    'Polish' => 'Wypieki'
                ]
            ],
            [
                'name' => 'Beer',
                'category' => 'Processed Food',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 43
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 0.46,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Carbohydrates',
                        'value' => 3.55,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.02,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 27,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Piwo'
                ]
            ],
            [
                'name' => 'Bread',
                'category' => 'Processed Food',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Chleb'
                ]
            ],
            [
                'name' => 'Wine',
                'category' => 'Processed Food',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Wino'
                ]
            ]
        ];
    }

    protected function meats(): array
    {
        return [
            [
                'name' => 'Beef',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 288
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 26.33,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 19.54,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 10,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.65,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 315,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Wołowina'
                ]
            ],
            [
                'name' => 'Chicken',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 164
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 24.82,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 6.48,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.89,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 204,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Kurczak'
                ]
            ],
            [
                'name' => 'Duck',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 132
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 18.28,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 5.95,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 11,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.4,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 271,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 24,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 5.8,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Kaczka'
                ]
            ],
            [
                'name' => 'Fish',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 84
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 17.76,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 0.92,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 13,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 351,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 20,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.5,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Ryby'
                ]
            ],
            [
                'name' => 'Gamebird',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'translations' => [
                    'Polish' => 'Ptactwo Łowne'
                ]
            ],
            [
                'name' => 'Geese',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 161
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 22.75,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 7.13,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 84,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 13,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.57,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 420,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 12,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 7.2,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Gęsi'
                ]
            ],
            [
                'name' => 'Goat',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 109
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 20.6,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 2.31,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 57,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 13,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 2.83,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 385,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Koza'
                ]
            ],
            [
                'name' => 'Lamb',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 292
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 24.32,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 20.77,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 96,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 17,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.87,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 307,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Owca'
                ]
            ],
            [
                'name' => 'Ostrich',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 155
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 28,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 3.9,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 93,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 6,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 3.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 353,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Struś'
                ]
            ],
            [
                'name' => 'Pork',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 271
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 27.34,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 17.04,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 90,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 25,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.09,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 351,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Vitamin A',
                        'value' => 2,
                        'value_unit' => Unit::ABBREVIATION_MICROGRAM
                    ],
                    [
                        'name' => 'Vitamin C',
                        'value' => 0.3,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Wieprzowina'
                ]
            ],
            [
                'name' => 'Rabbit',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 136
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 20.05,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 5.55,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 57,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 13,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.57,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 330,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Królik'
                ]
            ],
            [
                'name' => 'Turkey',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 119
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 21.77,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 2.86,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 65,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 70,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 14,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 1.45,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 296,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Indyk'
                ]
            ],
            [
                'name' => 'Veal',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 144
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 19.35,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 6.77,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 82,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 82,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Calcium',
                        'value' => 15,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Iron',
                        'value' => 0.83,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 315,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Cielęcina'
                ]
            ],
            [
                'name' => 'Venison',
                'category' => 'Meats',
                'system' => 1,
                'countries' => [
                    Country::NAME_POLAND,
                    'United States'
                ],
                'substances' => [
                    [
                        'name' => 'Calories',
                        'value' => 187
                    ],
                    [
                        'name' => 'Protein',
                        'value' => 26.5,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Fat',
                        'value' => 8.2,
                        'value_unit' => Unit::ABBREVIATION_GRAM
                    ],
                    [
                        'name' => 'Cholesterol',
                        'value' => 98,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Sodium',
                        'value' => 78,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                    [
                        'name' => 'Potassium',
                        'value' => 364,
                        'value_unit' => Unit::ABBREVIATION_MILIGRAM
                    ],
                ],
                'translations' => [
                    'Polish' => 'Dziczyzna'
                ]
            ]
        ];
    }
}
