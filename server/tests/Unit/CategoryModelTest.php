<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $category = Category::create([
            'name' => 'Web Development',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Web Development',
        ]);
    }

    #[Test]
    public function it_does_not_have_timestamps()
    {
        $category = Category::create([
            'name' => 'Mobile Development',
        ]);

        $this->assertNull($category->created_at);
        $this->assertNull($category->updated_at);
        $this->assertDatabaseMissing('categories', [
            'name' => 'Mobile Development',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_has_many_problems()
    {
        $category = Category::factory()->create();
        $company = User::factory()->create(['role' => 'company']);
        $problem = Problem::factory()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $category->problems);
        $this->assertCount(1, $category->problems);
        $this->assertTrue($category->problems->contains($problem));
    }

    #[Test]
    public function it_belongs_to_many_users()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create(['role' => 'provider']);
        $category->users()->attach($user);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $category->users);
        $this->assertCount(1, $category->users);
        $this->assertTrue($category->users->contains($user));
    }
}