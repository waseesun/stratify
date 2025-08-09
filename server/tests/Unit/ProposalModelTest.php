<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Problem;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProposalDocs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProposalModelTest extends TestCase
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

        $proposal = Proposal::create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Proposal for website redesign',
            'description' => 'Proposal for website redesign',
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('proposals', [
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Proposal for website redesign',
            'description' => 'Proposal for website redesign',
            'status' => 'submitted',
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

        $proposal = Proposal::create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Proposal for website redesign',
            'description' => 'Proposal for website redesign',
        ]);

        $this->assertNotNull($proposal->created_at);
        $this->assertNotNull($proposal->updated_at);
        $this->assertDatabaseHas('proposals', [
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'created_at' => $proposal->created_at,
            'updated_at' => $proposal->updated_at,
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
        $proposal = Proposal::create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Proposal for website redesign',
            'description' => 'Proposal for website redesign',
        ]);

        $this->assertInstanceOf(User::class, $proposal->provider);
        $this->assertEquals($provider->id, $proposal->provider->id);
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
        $proposal = Proposal::create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Proposal for website redesign',
            'description' => 'Proposal for website redesign',
        ]);

        $this->assertInstanceOf(Problem::class, $proposal->problem);
        $this->assertEquals($problem->id, $proposal->problem->id);
    }

    #[Test]
    public function it_has_one_project()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Proposal for website redesign',
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

        $this->assertInstanceOf(Project::class, $proposal->project);
        $this->assertEquals($project->id, $proposal->project->id);
    }

    #[Test]
    public function it_has_many_proposal_docs()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::create([
            'provider_id' => $provider->id,
            'problem_id' => $problem->id,
            'title' => 'Website Redesign Proposal',
            'description' => 'Proposal for website redesign',
            'status' => 'submitted',
        ]);
        $proposalDoc = ProposalDocs::create([
            'proposal_id' => $proposal->id,
            'file_url' => 'https://example.com/doc.pdf',
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $proposal->docs);
        $this->assertCount(1, $proposal->docs);
        $this->assertTrue($proposal->docs->contains($proposalDoc));
    }
}