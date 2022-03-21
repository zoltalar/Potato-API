<?php

declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CountryUnitSeeder::class);
        $this->call(StateSeeder::class);
        $this->call(CitySeeder::class);
    }
}
