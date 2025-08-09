<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Problem;
use App\Models\PortfolioLink;
use App\Models\Category;
use App\Models\Proposal;
use App\Models\Transaction;
use App\Models\Review;
use App\Models\Notification;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
            'address' => '123 Main St',
            'role' => 'company',
            'description' => 'Test user',
            'image_url' => 'http://example.com/image.jpg',
            'is_active' => true,
            'is_admin' => false,
            'is_super_admin' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'role' => 'company',
        ]);

        $this->assertTrue(Hash::check('StrongPass1!', $user->password));
    }

    #[Test]
    public function it_hides_sensitive_attributes()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    #[Test]
    public function it_casts_boolean_attributes()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => 1,
            'is_admin' => 0,
            'is_super_admin' => 1,
        ]);

        $this->assertIsBool($user->is_active);
        $this->assertTrue($user->is_active);
        $this->assertIsBool($user->is_admin);
        $this->assertFalse($user->is_admin);
        $this->assertIsBool($user->is_super_admin);
        $this->assertTrue($user->is_super_admin);
    }

    #[Test]
    public function it_hashes_password_when_set()
    {
        $user = new User();
        $user->password = 'StrongPass1!';

        $this->assertTrue(Hash::check('StrongPass1!', $user->password));
    }

    #[Test]
    public function it_does_not_rehash_already_hashed_password()
    {
        $hashed = Hash::make('StrongPass1!');
        $user = new User();
        $user->password = $hashed;

        $this->assertEquals($hashed, $user->password);
    }

    #[Test]
    public function it_validates_password_strength()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->password = 'weak';
    }

    #[Test]
    public function it_validates_password_length()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatches('/at least 8 characters/');

        $user = new User();
        $user->password = 'Ab1!';
    }

    #[Test]
    public function it_validates_password_lowercase()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatches('/lowercase letter/');

        $user = new User();
        $user->password = 'ABC123!!';
    }

    #[Test]
    public function it_validates_password_uppercase()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatches('/uppercase letter/');

        $user = new User();
        $user->password = 'abc123!!';
    }

    #[Test]
    public function it_validates_password_number()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatches('/at least one number/');

        $user = new User();
        $user->password = 'Abcdefg!';
    }

    #[Test]
    public function it_validates_password_special_character()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatches('/special character/');

        $user = new User();
        $user->password = 'Abcdefg1';
    }

    #[Test]
    public function it_generates_slug_from_email()
    {
        $user = User::factory()->create(['email' => 'john.doe@example.com', 'role' => 'admin']);

        $this->assertEquals('johndoe-at-examplecom', $user->slug);
    }

    #[Test]
    public function it_checks_if_user_is_company()
    {
        $user = User::factory()->create(['role' => 'company']);

        $this->assertTrue($user->isCompany());

        $user->role = 'provider';
        $this->assertFalse($user->isCompany());
    }

    #[Test]
    public function it_checks_if_user_is_provider()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isProvider());

        $user->role = 'company';
        $this->assertFalse($user->isProvider());
    }

    #[Test]
    public function it_checks_if_user_is_admin()
    {
        $user = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $this->assertTrue($user->isAdmin());

        $user->is_admin = false;
        $this->assertFalse($user->isAdmin());
    }

    #[Test]
    public function it_checks_if_user_is_super_admin()
    {
        $user = User::factory()->create(['is_super_admin' => true, 'role' => 'admin']);
        $this->assertTrue($user->isSuperAdmin());

        $user->is_super_admin = false;
        $this->assertFalse($user->isSuperAdmin());
    }

    #[Test]
    public function it_has_many_problems()
    {
        $user = User::factory()->create(['role' => 'company']);
        $problem = Problem::factory()->create(['company_id' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->problems);
        $this->assertCount(1, $user->problems);
        $this->assertTrue($user->problems->contains($problem));
    }

    #[Test]
    public function it_has_many_portfolio_links()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $link = PortfolioLink::factory()->create(['provider_id' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->portfolioLinks);
        $this->assertCount(1, $user->portfolioLinks);
        $this->assertTrue($user->portfolioLinks->contains($link));
    }

    #[Test]
    public function it_belongs_to_many_categories()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $user->categories()->attach($category);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->categories);
        $this->assertCount(1, $user->categories);
        $this->assertTrue($user->categories->contains($category));
    }

    #[Test]
    public function it_has_many_proposals()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $user->id,
            'problem_id' => $problem->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->proposals);
        $this->assertCount(1, $user->proposals);
        $this->assertTrue($user->proposals->contains($proposal));
    }

    #[Test]
    public function it_has_many_provider_transactions()
    {
        $user = User::factory()->create(['role' => 'provider']);
        $company = User::factory()->create(['role' => 'company']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
        ]);
        $proposal = Proposal::factory()->create([
            'provider_id' => $user->id,
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
        $transaction = Transaction::factory()->create([
            'provider_id' => $user->id,
            'company_id' => $company->id,
            'project_id' => $project->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->providerTransactions);
        $this->assertCount(1, $user->providerTransactions);
        $this->assertTrue($user->providerTransactions->contains($transaction));
    }

    #[Test]
    public function it_has_many_company_transactions()
    {
        $user = User::factory()->create(['role' => 'company']);
        $provider = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $problem = Problem::factory()->create([
            'company_id' => $user->id,
            'category_id' => $category->id,
        ]);
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
        $transaction = Transaction::factory()->create([
            'provider_id' => $provider->id,
            'company_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->companyTransactions);
        $this->assertCount(1, $user->companyTransactions);
        $this->assertTrue($user->companyTransactions->contains($transaction));
    }

    #[Test]
    public function it_has_many_reviews_given()
    {
        $user = User::factory()->create(['role' => 'company']);
        $reviewee = User::factory()->create(['role' => 'provider']);
        $review = Review::factory()->create([
            'reviewer_id' => $user->id,
            'reviewee_id' => $reviewee->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->reviewsGiven);
        $this->assertCount(1, $user->reviewsGiven);
        $this->assertTrue($user->reviewsGiven->contains($review));
    }

    #[Test]
    public function it_has_many_reviews_received()
    {
        $user = User::factory()->create(['role' => 'company']);
        $reviewer = User::factory()->create(['role' => 'provider']);
        $review = Review::factory()->create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $user->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->reviewsReceived);
        $this->assertCount(1, $user->reviewsReceived);
        $this->assertTrue($user->reviewsReceived->contains($review));
    }

    #[Test]
    public function it_has_many_notifications()
    {
        $user = User::factory()->create(['role' => 'company']);
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->notifications);
        $this->assertCount(1, $user->notifications);
        $this->assertTrue($user->notifications->contains($notification));
    }
}