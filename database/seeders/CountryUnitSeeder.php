<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class CountryUnitSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->matrix() as $countryName => $unitAbbreviations) {
            $country = Country::query()
                ->where('name', $countryName)
                ->first();

            if ($country !== null) {
                $units = Unit::query()
                    ->whereIn('abbreviation', $unitAbbreviations)
                    ->get();

                if ($units !== null) {
                    $country->units()->sync($units->pluck('id')->toArray());
                }
            }
        }
    }

    protected function matrix(): array
    {
        return [
            Country::NAME_POLAND => [
                Unit::ABBREVIATION_KILOGRAM,
                Unit::ABBREVIATION_KILOMETER,
                Unit::ABBREVIATION_LITER,
                Unit::ABBREVIATION_QUANTITY
            ]
        ];
    }
}
