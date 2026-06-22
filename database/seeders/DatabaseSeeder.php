<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    \App\Models\Category::insert([
        ['name' => 'Breakfast', 'slug' => 'breakfast'],
        ['name' => 'Lunch', 'slug' => 'lunch'],
        ['name' => 'Dinner', 'slug' => 'dinner'],
        ['name' => 'Dessert', 'slug' => 'dessert'],
        ['name' => 'Vegan', 'slug' => 'vegan'],
    ]);
}
}
