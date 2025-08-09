<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use App\Models\Problem;
use App\Models\Category;
use App\Models\Proposal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Website Redesign Proposal',
            'description' => 'Proposal for website redesign',
            'status' => 'accepted',
        ]);

        $project = Project::create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        $this->assertDatabaseHas('projects', [
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
        ]);
    }

    #[Test]
    public function it_has_timestamps()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Website Redesign Proposal',
            'description' => 'Proposal for website redesign',
            'status' => 'accepted',
        ]);

        $project = Project::create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        $this->assertNotNull($project->created_at);
        $this->assertNotNull($project->updated_at);
        $this->assertDatabaseHas('projects', [
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ]);
    }

    #[Test]
    public function it_belongs_to_problem()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Website Redesign Proposal',
            'description' => 'Proposal for website redesign',
            'status' => 'accepted',
        ]);
        $project = Project::create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        $this->assertInstanceOf(Problem::class, $project->problem);
        $this->assertEquals($problem->id, $project->problem->id);
    }

    #[Test]
    public function it_belongs_to_proposal()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Website Redesign Proposal',
            'description' => 'Proposal for website redesign',
            'status' => 'accepted',
        ]);
        $project = Project::create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        $this->assertInstanceOf(Proposal::class, $project->proposal);
        $this->assertEquals($proposal->id, $project->proposal->id);
    }

    #[Test]
    public function it_has_many_transactions()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Website Redesign Proposal',
            'description' => 'Proposal for website redesign',
            'status' => 'accepted',
        ]);
        $project = Project::create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        $transaction = Transaction::factory()->create([
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
            'release_date' => now(),
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $project->transactions);
        $this->assertCount(1, $project->transactions);
        $this->assertTrue($project->transactions->contains($transaction));
    }
}