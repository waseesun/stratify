<?php

namespace Tests\Unit;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ReviewModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a review can be created with mass-assignable attributes.
     */
    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes(): void
    {
        // We create two users with specific roles to satisfy potential constraints,
        // and to test the relationships correctly.
        $reviewer = User::factory()->company()->create();
        $reviewee = User::factory()->provider()->create();

        $review = Review::create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
            'rating' => 5,
            'comment' => 'This is a great service provider!',
        ]);

        $this->assertDatabaseHas('reviews', [
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
            'rating' => 5,
            'comment' => 'This is a great service provider!',
        ]);

        $this->assertInstanceOf(Review::class, $review);
    }

    /**
     * Test the reviewer relationship.
     */
    #[Test]
    public function it_has_a_reviewer_relationship(): void
    {
        $reviewer = User::factory()->company()->create();
        $reviewee = User::factory()->provider()->create();
        $review = Review::factory()->create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
        ]);

        $this->assertInstanceOf(User::class, $review->reviewer);
        $this->assertEquals($reviewer->id, $review->reviewer->id);
    }

    /**
     * Test the reviewee relationship.
     */
    #[Test]
    public function it_has_a_reviewee_relationship(): void
    {
        $reviewee = User::factory()->provider()->create();
        $reviewer = User::factory()->company()->create();
        $review = Review::factory()->create([
            'reviewee_id' => $reviewee->id,
            'reviewer_id' => $reviewer->id,
        ]);

        $this->assertInstanceOf(User::class, $review->reviewee);
        $this->assertEquals($reviewee->id, $review->reviewee->id);
    }

    /**
     * Test the User's reviewsGiven relationship.
     */
    #[Test]
    public function it_is_part_of_a_user_s_reviews_given_relationship(): void
    {
        $reviewer = User::factory()->company()->create();
        $reviewee = User::factory()->provider()->create();
        Review::factory()->create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
        ]);

        $this->assertCount(1, $reviewer->reviewsGiven);
        $this->assertInstanceOf(Review::class, $reviewer->reviewsGiven->first());
    }

    /**
     * Test the User's reviewsReceived relationship.
     */
    #[Test]
    public function it_is_part_of_a_user_s_reviews_received_relationship(): void
    {
        $reviewer = User::factory()->company()->create();
        $reviewee = User::factory()->provider()->create();
        Review::factory()->create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
        ]);

        $this->assertCount(1, $reviewee->reviewsReceived);
        $this->assertInstanceOf(Review::class, $reviewee->reviewsReceived->first());
    }
}