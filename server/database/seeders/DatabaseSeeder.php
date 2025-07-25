<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Category;
use App\Models\Problem;
use App\Models\PortfolioLink;
use App\Models\ProblemSkillset;
use Illuminate\Database\Seeder;

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

        Problem::factory()->count(10)->create();

        PortfolioLink::factory()->count(15)->create();

        ProblemSkillset::factory()->count(20)->create();
    }
}
