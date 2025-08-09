<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PortfolioLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PortfolioLinkModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $user = User::factory()->create(['role' => 'provider']);

        $portfolioLink = PortfolioLink::create([
            'link' => 'https://example.com/portfolio',
            'provider_id' => $user->id,
        ]);

        $this->assertDatabaseHas('portfolio_links', [
            'link' => 'https://example.com/portfolio',
            'provider_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_does_not_have_timestamps()
    {
        $user = User::factory()->create(['role' => 'provider']);

        $portfolioLink = PortfolioLink::create([
            'link' => 'https://example.com/portfolio',
            'provider_id' => $user->id,
        ]);

        $this->assertNull($portfolioLink->created_at);
        $this->assertNull($portfolioLink->updated_at);
        $this->assertDatabaseMissing('portfolio_links', [
            'link' => 'https://example.com/portfolio',
            'provider_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_belongs_to_provider()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $portfolioLink = PortfolioLink::create([
            'link' => 'https://example.com/portfolio',
            'provider_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $portfolioLink->provider);
        $this->assertEquals($user->id, $portfolioLink->provider->id);
    }
}