<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commonCategories = [
            ['name' => 'Groceries', 'description' => 'Food and household groceries'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, gas, internet'],
            ['name' => 'Rent', 'description' => 'Monthly rent and housing costs'],
            ['name' => 'Transportation', 'description' => 'Fuel, public transport, and travel'],
            ['name' => 'Maintenance', 'description' => 'Repairs and home maintenance'],
            ['name' => 'Other', 'description' => 'Miscellaneous shared expenses'],
        ];

        Colocation::query()->each(function (Colocation $colocation) use ($commonCategories): void {
            foreach ($commonCategories as $categoryData) {
                Category::query()->firstOrCreate(
                    [
                        'colocation_id' => $colocation->id,
                        'name' => $categoryData['name'],
                    ],
                    [
                        'description' => $categoryData['description'],
                    ]
                );
            }
        });
    }
}
