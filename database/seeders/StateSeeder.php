<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Arr;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /** @var array */
    protected $cache = [];

    public function run()
    {
        foreach ($this->states() as $state) {
            $countryName = $state['country'];
            unset($state['country']);

            if ( ! isset($this->cache['countries'][$countryName])) {
                $country = Country::query()
                    ->where('name', $countryName)
                    ->first();

                if ($country !== null) {
                    $this->cache['countries'][$countryName] = $country;
                }
            }

            if (isset($this->cache['countries'][$countryName])) {
                $country = $this->cache['countries'][$countryName];
                $state['country_id'] = $country->id;

                State::firstOrCreate(
                    Arr::only($state, ['name', 'country_id']),
                    Arr::except($state, ['name', 'country_id'])
                );
            }
        }
    }

    protected function states(): array
    {
        return array_merge(
            $this->usStates(),
            $this->polishStates()
        );
    }

    protected function usStates(): array
    {
        return [
            [
                'name' => 'Alabama',
                'abbreviation' => 'AL',
                'country' => 'United States'
            ],
            [
                'name' => 'Alaska',
                'abbreviation' => 'AK',
                'country' => 'United States'
            ],
            [
                'name' => 'Arizona',
                'abbreviation' => 'AZ',
                'country' => 'United States'
            ],
            [
                'name' => 'Arkansas',
                'abbreviation' => 'AR',
                'country' => 'United States'
            ],
            [
                'name' => 'California',
                'abbreviation' => 'CA',
                'country' => 'United States'
            ],
            [
                'name' => 'Colorado',
                'abbreviation' => 'CO',
                'country' => 'United States'
            ],
            [
                'name' => 'Connecticut',
                'abbreviation' => 'CT',
                'country' => 'United States'
            ],
            [
                'name' => 'Delaware',
                'abbreviation' => 'DE',
                'country' => 'United States'
            ],
            [
                'name' => 'District of Columbia',
                'abbreviation' => 'DC',
                'country' => 'United States'
            ],
            [
                'name' => 'Florida',
                'abbreviation' => 'FL',
                'country' => 'United States'
            ],
            [
                'name' => 'Georgia',
                'abbreviation' => 'GA',
                'country' => 'United States'
            ],
            [
                'name' => 'Guam',
                'abbreviation' => 'GU',
                'country' => 'United States'
            ],
            [
                'name' => 'Hawaii',
                'abbreviation' => 'HI',
                'country' => 'United States'
            ],
            [
                'name' => 'Idaho',
                'abbreviation' => 'ID',
                'country' => 'United States'
            ],
            [
                'name' => 'Illinois',
                'abbreviation' => 'IL',
                'country' => 'United States'
            ],
            [
                'name' => 'Indiana',
                'abbreviation' => 'IN',
                'country' => 'United States'
            ],
            [
                'name' => 'Iowa',
                'abbreviation' => 'IA',
                'country' => 'United States'
            ],
            [
                'name' => 'Kansas',
                'abbreviation' => 'KS',
                'country' => 'United States'
            ],
            [
                'name' => 'Kentucky',
                'abbreviation' => 'KY',
                'country' => 'United States'
            ],
            [
                'name' => 'Louisiana',
                'abbreviation' => 'LA',
                'country' => 'United States'
            ],
            [
                'name' => 'Maine',
                'abbreviation' => 'ME',
                'country' => 'United States'
            ],
            [
                'name' => 'Marshall Islands',
                'abbreviation' => 'MH',
                'country' => 'United States'
            ],
            [
                'name' => 'Maryland',
                'abbreviation' => 'MD',
                'country' => 'United States'
            ],
            [
                'name' => 'Massachusetts',
                'abbreviation' => 'MA',
                'country' => 'United States'
            ],
            [
                'name' => 'Michigan',
                'abbreviation' => 'MI',
                'country' => 'United States'
            ],
            [
                'name' => 'Mississippi',
                'abbreviation' => 'MS',
                'country' => 'United States'
            ],
            [
                'name' => 'Missouri',
                'abbreviation' => 'MO',
                'country' => 'United States'
            ],
            [
                'name' => 'Montana',
                'abbreviation' => 'MT',
                'country' => 'United States'
            ],
            [
                'name' => 'Nebraska',
                'abbreviation' => 'NE',
                'country' => 'United States'
            ],
            [
                'name' => 'Nevada',
                'abbreviation' => 'NV',
                'country' => 'United States'
            ],
            [
                'name' => 'New Hampshire',
                'abbreviation' => 'NH',
                'country' => 'United States'
            ],
            [
                'name' => 'New Jersey',
                'abbreviation' => 'NJ',
                'country' => 'United States'
            ],
            [
                'name' => 'New Mexico',
                'abbreviation' => 'NM',
                'country' => 'United States'
            ],
            [
                'name' => 'New York',
                'abbreviation' => 'NY',
                'country' => 'United States'
            ],
            [
                'name' => 'North Carolina',
                'abbreviation' => 'NC',
                'country' => 'United States'
            ],
            [
                'name' => 'North Dakota',
                'abbreviation' => 'ND',
                'country' => 'United States'
            ],
            [
                'name' => 'Ohio',
                'abbreviation' => 'OH',
                'country' => 'United States'
            ],
            [
                'name' => 'Oklahoma',
                'abbreviation' => 'OK',
                'country' => 'United States'
            ],
            [
                'name' => 'Oregon',
                'abbreviation' => 'OR',
                'country' => 'United States'
            ],
            [
                'name' => 'Pennsylvania',
                'abbreviation' => 'PA',
                'country' => 'United States'
            ],
            [
                'name' => 'Puerto Rico',
                'abbreviation' => 'PR',
                'country' => 'United States'
            ],
            [
                'name' => 'Rhode Island',
                'abbreviation' => 'RI',
                'country' => 'United States'
            ],
            [
                'name' => 'South Carolina',
                'abbreviation' => 'SC',
                'country' => 'United States'
            ],
            [
                'name' => 'South Dakota',
                'abbreviation' => 'SD',
                'country' => 'United States'
            ],
            [
                'name' => 'Tennessee',
                'abbreviation' => 'TN',
                'country' => 'United States'
            ],
            [
                'name' => 'Texas',
                'abbreviation' => 'TX',
                'country' => 'United States'
            ],
            [
                'name' => 'Utah',
                'abbreviation' => 'UT',
                'country' => 'United States'
            ],
            [
                'name' => 'Vermont',
                'abbreviation' => 'VT',
                'country' => 'United States'
            ],
            [
                'name' => 'Virginia',
                'abbreviation' => 'VA',
                'country' => 'United States'
            ],
            [
                'name' => 'Washington',
                'abbreviation' => 'WA',
                'country' => 'United States'
            ],
            [
                'name' => 'West Virginia',
                'abbreviation' => 'WV',
                'country' => 'United States'
            ],
            [
                'name' => 'Wisconsin',
                'abbreviation' => 'WI',
                'country' => 'United States'
            ],
            [
                'name' => 'Wyoming',
                'abbreviation' => 'WY',
                'country' => 'United States'
            ],
        ];
    }

    protected function polishStates(): array
    {
        return [
            [
                'name' => 'Dolnośląskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Kujawsko-Pomorskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Lubelskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Lubuskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Łódzkie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Małopolskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Mazowieckie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Opolskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Podkarpackie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Podlaskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Pomorskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Śląskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Świętokrzyskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Warmińsko-Mazurskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Wielkopolskie',
                'country' => 'Poland'
            ],
            [
                'name' => 'Zachodniopomorskie',
                'country' => 'Poland'
            ]
        ];
    }
}
