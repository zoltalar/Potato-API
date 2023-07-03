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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
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
                    Country::NAME_POLAND,
                    'United States'
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
                'translations' => [
                    'Polish' => 'Kawa'
                ]
            ],
            [
                'name' => 'Black Tea',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [],
                'translations' => [
                    'Polish' => 'Czarna Herbata'
                ]
            ],
            [
                'name' => 'Green Tea',
                'category' => 'Coffee and Teas',
                'system' => 1,
                'countries' => [],
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
                'translations' => [
                    'Polish' => 'Dziczyzna'
                ]
            ]
        ];
    }
}
