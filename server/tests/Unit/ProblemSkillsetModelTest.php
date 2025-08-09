<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ProblemSkillset;
use App\Models\Problem;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProblemSkillsetModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $skillset = ProblemSkillset::create([
            'problem_id' => $problem->id,
            'skill' => 'PHP',
        ]);

        $this->assertDatabaseHas('problem_skillsets', [
            'problem_id' => $problem->id,
            'skill' => 'PHP',
        ]);
    }

    #[Test]
    public function it_does_not_have_timestamps()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $skillset = ProblemSkillset::create([
            'problem_id' => $problem->id,
            'skill' => 'PHP',
        ]);

        $this->assertNull($skillset->created_at);
        $this->assertNull($skillset->updated_at);
        $this->assertDatabaseMissing('problem_skillsets', [
            'problem_id' => $problem->id,
            'skill' => 'PHP',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_belongs_to_problem()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $skillset = ProblemSkillset::create([
            'problem_id' => $problem->id,
            'skill' => 'PHP',
        ]);

        $this->assertInstanceOf(Problem::class, $skillset->problem);
        $this->assertEquals($problem->id, $skillset->problem->id);
    }
}