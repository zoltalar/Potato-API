<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Language;
use Arr;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->languages() as $language) {
            Language::firstOrCreate(
                Arr::only($language, ['name', 'code']),
                Arr::except($language, ['name', 'code'])
            );
        }
    }

    protected function languages(): array
    {
        return [
            [
                'name' => Language::NAME_ENGLISH,
                'native' => 'English',
                'code' => Language::CODE_EN,
                'system' => 1,
                'active' => 1
            ],
            [
                'name' => Language::NAME_POLISH,
                'native' => 'Polski',
                'code' => Language::CODE_PL,
                'system' => 1,
                'active' => 1
            ],
        ];
    }
}
