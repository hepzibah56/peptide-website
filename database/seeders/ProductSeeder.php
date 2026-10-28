<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\DatabaseSeeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Products are created and managed in the admin panel.
        // This seeder intentionally adds no catalogue data.
    }
}