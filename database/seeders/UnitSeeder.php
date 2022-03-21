<?php

namespace Database\Seeders;

use App\Models\Unit;
use Arr;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->units() as $unit) {
            Unit::firstOrCreate(
                Arr::only($unit, ['abbreviation', 'name']),
                Arr::except($unit, ['abbreviation', 'name'])
            );
        }
    }

    protected function units(): array
    {
        return [
            [
                'abbreviation' => Unit::ABBREVIATION_KILOGRAM,
                'name' => 'kilogram',
                'type' => Unit::TYPE_WEIGHT,
                'system' => Unit::SYSTEM_METRIC
            ],
            [
                'abbreviation' => Unit::ABBREVIATION_KILOMETER,
                'name' => 'kilometer',
                'type' => Unit::TYPE_LENGTH,
                'system' => Unit::SYSTEM_METRIC
            ],
            [
                'abbreviation' => Unit::ABBREVIATION_LITER,
                'name' => 'liter',
                'type' => Unit::TYPE_VOLUME,
                'system' => Unit::SYSTEM_METRIC
            ],
            [
                'abbreviation' => Unit::ABBREVIATION_POUND,
                'name' => 'pound',
                'type' => Unit::TYPE_WEIGHT,
                'system' => Unit::SYSTEM_IMPERIAL
            ],
            [
                'abbreviation' => Unit::ABBREVIATION_MILE,
                'name' => 'mile',
                'type' => Unit::TYPE_LENGTH,
                'system' => Unit::SYSTEM_IMPERIAL
            ],
            [
                'abbreviation' => Unit::ABBREVIATION_GALLON,
                'name' => 'gallon',
                'type' => Unit::TYPE_VOLUME,
                'system' => Unit::SYSTEM_IMPERIAL
            ]
        ];
    }
}
