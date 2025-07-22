<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
    }
}
