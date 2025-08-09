<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Problem;
use App\Models\Category;
use App\Models\Proposal;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TransactionModelTest extends TestCase
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
        $project = Project::factory()->create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        $transaction = Transaction::create([
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
            'release_date' => now(),
        ]);

        $this->assertDatabaseHas('transactions', [
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
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
        $project = Project::factory()->create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        $transaction = Transaction::create([
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
            'release_date' => now(),
        ]);

        $this->assertNotNull($transaction->created_at);
        $this->assertNotNull($transaction->updated_at);
        $this->assertDatabaseHas('transactions', [
            'provider_id' => $provider->id,
            'project_id' => $project->id,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ]);
    }

    #[Test]
    public function it_belongs_to_provider()
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
        $project = Project::factory()->create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        $transaction = Transaction::create([
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
            'release_date' => now(),
        ]);

        $this->assertInstanceOf(User::class, $transaction->provider);
        $this->assertEquals($provider->id, $transaction->provider->id);
    }

    #[Test]
    public function it_belongs_to_company()
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
        $project = Project::factory()->create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        $transaction = Transaction::create([
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
            'release_date' => now(),
        ]);

        $this->assertInstanceOf(User::class, $transaction->company);
        $this->assertEquals($company->id, $transaction->company->id);
    }

    #[Test]
    public function it_belongs_to_project()
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
        $project = Project::factory()->create([
            'problem_id' => $problem->id,
            'proposal_id' => $proposal->id,
            'fee' => 1000,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        $transaction = Transaction::create([
            'provider_id' => $provider->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
            'milestone_name' => 'Initial Payment',
            'amount' => 500,
            'release_date' => now(),
        ]);

        $this->assertInstanceOf(Project::class, $transaction->project);
        $this->assertEquals($project->id, $transaction->project->id);
    }
}