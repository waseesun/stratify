<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create();

        User::factory()->superAdmin()->create();
        
        User::factory()->company()->count(4)->create();
        
        User::factory()->provider()->count(4)->create();

        User::factory()->inactive()->create();

        Category::factory()->count(5)->create();
    }
}
