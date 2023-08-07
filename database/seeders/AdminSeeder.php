<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Admin;
use Arr;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->admins() as $admin) {
            Admin::firstOrCreate(
                Arr::only($admin, ['email']),
                Arr::except($admin, ['email'])
            );
        }
    }

    protected function admins(): array
    {
        return [];
    }
}
