<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Problem;
use App\Models\User;
use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\ProblemSkillset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProblemModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();

        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
            'timeline_unit' => 'week',
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('problems', [
            'title' => 'Website Redesign',
            'company_id' => $user->id,
            'category_id' => $category->id,
            'budget' => 5000,
            'timeline_value' => 3,
            'timeline_unit' => 'week',
            'status' => 'open',
        ]);
    }

    #[Test]
    public function it_has_timestamps()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();

        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
        ]);

        $this->assertNotNull($problem->created_at);
        $this->assertNotNull($problem->updated_at);
        $this->assertDatabaseHas('problems', [
            'title' => 'Website Redesign',
            'created_at' => $problem->created_at,
            'updated_at' => $problem->updated_at,
        ]);
    }

    #[Test]
    public function it_belongs_to_company()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
        ]);

        $this->assertInstanceOf(User::class, $problem->company);
        $this->assertEquals($user->id, $problem->company->id);
    }

    #[Test]
    public function it_belongs_to_category()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
        ]);

        $this->assertInstanceOf(Category::class, $problem->category);
        $this->assertEquals($category->id, $problem->category->id);
    }

    #[Test]
    public function it_belongs_to_project()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
        ]);
        $provider = User::factory()->create(['role' => 'provider']);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
        ]);
        $project = Project::factory()->create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        $problem->update(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $problem->project);
        $this->assertEquals($project->id, $problem->project->id);
    }

    #[Test]
    public function it_has_many_proposals()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
        ]);
        $provider = User::factory()->create(['role' => 'provider']);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $problem->proposals);
        $this->assertCount(1, $problem->proposals);
        $this->assertTrue($problem->proposals->contains($proposal));
    }

    #[Test]
    public function it_has_many_skillsets()
    {
        $user = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::create([
            'title' => 'Website Redesign',
            'description' => 'Need a modern website redesign',
            'budget' => 5000,
            'company_id' => $user->id,
            'category_id' => $category->id,
            'timeline_value' => 3,
        ]);
        $skillset = ProblemSkillset::factory()->create([
            'problem_id' => $problem->id,
            'skill' => 'PHP',
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $problem->skillsets);
        $this->assertCount(1, $problem->skillsets);
        $this->assertTrue($problem->skillsets->contains($skillset));
    }
}