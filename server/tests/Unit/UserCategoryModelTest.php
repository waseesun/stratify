<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\UserCategory;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserCategoryModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();

        $userCategory = UserCategory::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('user_categories', [
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    #[Test]
    public function it_does_not_have_timestamps()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();

        $userCategory = UserCategory::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertNull($userCategory->created_at);
        $this->assertNull($userCategory->updated_at);
        $this->assertDatabaseMissing('user_categories', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_casts_attributes_to_integer()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();

        $userCategory = UserCategory::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertIsInt($userCategory->user_id);
        $this->assertIsInt($userCategory->category_id);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $userCategory = UserCategory::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(User::class, $userCategory->user);
        $this->assertEquals($user->id, $userCategory->user->id);
    }

    #[Test]
    public function it_belongs_to_category()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $userCategory = UserCategory::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $userCategory->category);
        $this->assertEquals($category->id, $userCategory->category->id);
    }
}