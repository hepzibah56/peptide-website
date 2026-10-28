<?php

namespace Database\Seeders;

use Illuminate\DatabaseSeeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Peptides', 'slug' => 'peptides', 'description' => 'Synthesized peptide compounds for research use.', 'sort' => 1],
            ['name' => 'Peptide Blends', 'slug' => 'blends', 'description' => 'Pre-formulated peptide combinations.', 'sort' => 2],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Syringes, needles, pens and diluents.', 'sort' => 3],
            ['name' => 'Bacteriostatic Water', 'slug' => 'bac-water', 'description' => 'Sterile water for dilution.', 'sort' => 4],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true])
            );
        }
    }
}