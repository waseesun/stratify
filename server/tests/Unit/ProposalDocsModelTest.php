<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ProposalDocs;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Problem;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProposalDocsModelTest extends TestCase
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
        ]);

        $proposalDoc = ProposalDocs::create([
            'proposal_id' => $proposal->id,
            'file_url' => 'https://example.com/doc.pdf',
        ]);

        $this->assertDatabaseHas('proposal_docs', [
            'proposal_id' => $proposal->id,
            'file_url' => 'https://example.com/doc.pdf',
        ]);
    }

    #[Test]
    public function it_does_not_have_timestamps()
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
        ]);

        $proposalDoc = ProposalDocs::create([
            'proposal_id' => $proposal->id,
            'file_url' => 'https://example.com/doc.pdf',
        ]);

        $this->assertNull($proposalDoc->created_at);
        $this->assertNull($proposalDoc->updated_at);
        $this->assertDatabaseMissing('proposal_docs', [
            'proposal_id' => $proposal->id,
            'file_url' => 'https://example.com/doc.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
        ]);
        $proposalDoc = ProposalDocs::create([
            'proposal_id' => $proposal->id,
            'file_url' => 'https://example.com/doc.pdf',
        ]);

        $this->assertInstanceOf(Proposal::class, $proposalDoc->proposal);
        $this->assertEquals($proposal->id, $proposalDoc->proposal->id);
    }
}