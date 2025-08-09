<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NotificationModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a notification can be created with mass-assignable attributes.
     */
    #[Test]
    public function it_can_be_created_with_mass_assignable_attributes(): void
    {
        // A user is required to satisfy the foreign key constraint.
        $user = User::factory()->company()->create();

        $notification = Notification::create([
            'user_id' => $user->id,
            'message' => 'Your problem has been reviewed.',
            'type' => 'problem',
            'link' => '/problems/1',
            'is_read' => true,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'message' => 'Your problem has been reviewed.',
            'type' => 'problem',
            'link' => '/problems/1',
            'is_read' => true,
        ]);

        $this->assertInstanceOf(Notification::class, $notification);
    }

    /**
     * Test that the user relationship works.
     */
    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $user = User::factory()->company()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertEquals($user->id, $notification->user->id);
    }

    /**
     * Test that the 'is_read' attribute defaults to false.
     */
    #[Test]
    public function is_read_defaults_to_false(): void
    {
        $user = User::factory()->company()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => 0,
        ]);
        $this->assertFalse($notification->is_read);
    }

    /**
     * Test that a user can have multiple notifications.
     */
    #[Test]
    public function it_is_part_of_a_user_s_notifications_relationship(): void
    {
        $user = User::factory()->company()->hasNotifications(3)->create();

        $this->assertCount(3, $user->notifications);
        $this->assertInstanceOf(Notification::class, $user->notifications->first());
    }
}
