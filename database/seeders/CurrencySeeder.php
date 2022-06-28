<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        foreach ($this->currencies() as $currency) {
            Currency::firstOrCreate($currency);
        }
    }

    protected function currencies(): array
    {
        return [
            [
                'name' => Currency::NAME_US_DOLLAR,
                'code' => 'USD',
                'symbol' => '$',
                'number' => 840
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'number' => 978
            ],
            [
                'name' => Currency::NAME_POLISH_ZLOTY,
                'code' => 'PLN',
                'symbol' => 'zł'
            ]
        ];
    }
}
