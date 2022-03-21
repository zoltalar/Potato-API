<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Country;
use Arr;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run()
    {
        foreach ($this->countries() as $country) {
            Country::firstOrCreate(
                Arr::only($country, ['name', 'code']),
                Arr::except($country, ['name', 'code'])
            );
        }
    }

    protected function countries(): array
    {
        return [
            [
                'name' => Country::NAME_UNITED_STATES,
                'native' => 'United States',
                'code' => 'us',
                'date_format' => 'MM/DD/YYYY',
                'time_format' => 'h:mm a',
                'system' => 1,
                'active' => 1
            ],
            [
                'name' => Country::NAME_POLAND,
                'native' => 'Polska',
                'code' => 'pl',
                'date_format' => 'DD/MM/YYYY',
                'time_format' => 'H:mm',
                'system' => 1,
                'active' => 1
            ],
        ];
    }
}
