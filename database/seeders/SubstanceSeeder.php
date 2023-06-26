<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Substance;
use App\Models\Translation;
use Arr;
use Illuminate\Database\Seeder;

class SubstanceSeeder extends Seeder
{
    /** @var array */
    protected $cache = [];

    public function run()
    {
        foreach ($this->substrances() as $substance) {
            $translations = $substance['translations'] ?? [];
            unset($substance['translations']);

            $substance = Substance::firstOrCreate(
                Arr::only($substance, ['name']),
                Arr::except($substance, ['name'])
            );

            // Add translations
            if ($substance !== null) {
                $translations = [Language::NAME_ENGLISH => $substance->name] + $translations;

                foreach ($translations as $languageName => $substanceName) {
                    if ( ! isset($this->cache['languages'][$languageName])) {
                        $language = Language::where('name', $languageName)->first();

                        if ($language !== null) {
                            $this->cache['languages'][$languageName] = $language;
                        }
                    }

                    if (isset($this->cache['languages'][$languageName])) {
                        $language = $this->cache['languages'][$languageName];

                        if ($substance->translation($language) === null) {
                            $attributes = ['name' => $substanceName, 'language_id' => $language->id];
                            $substance->translations()->save(new Translation($attributes));
                        }
                    }
                }
            }
        }
    }

    protected function substrances(): array
    {
        return [
            [
                'name' => 'Calcium',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Wapń'
                ]
            ],
            [
                'name' => 'Calories',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Kalorie'
                ]
            ],
            [
                'name' => 'Carbohydrates',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Węglowodany'
                ]
            ],
            [
                'name' => 'Cholesterol',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Cholesterol'
                ]
            ],
            [
                'name' => 'Fat',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Tłuszcze'
                ]
            ],
            [
                'name' => 'Fiber',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Błonnik'
                ]
            ],
            [
                'name' => 'Iron',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Żelazo'
                ]
            ],
            [
                'name' => 'Magnesium',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Magnez'
                ]
            ],
            [
                'name' => 'Potassium',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Potas'
                ]
            ],
            [
                'name' => 'Protein',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Proteiny'
                ]
            ],
            [
                'name' => 'Sodium',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Sód'
                ]
            ],
            [
                'name' => 'Sugar',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Cukry'
                ]
            ],
            [
                'name' => 'Vitamin B6',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Witamina B6'
                ]
            ],
            [
                'name' => 'Vitamin C',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Witamina C'
                ]
            ],
            [
                'name' => 'Vitamin D',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Witamina D'
                ]
            ],
            [
                'name' => 'Water',
                'active' => 1,
                'translations' => [
                    Language::NAME_POLISH => 'Woda'
                ]
            ],
        ];
    }
}
