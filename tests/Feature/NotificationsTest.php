<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->be(factory('App\Models\User')->create());
    }

    /** @test */
    function test_a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_left_by_not_current_user()
    {
        $thread = factory('App\Models\Thread')->create()->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        // creating reply for authenticated user
        $thread->addReply([
           'user_id' => auth()->id(),
           'body' => 'Some reply here'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        // creating reply of some other user
        $thread->addReply([
            'user_id' => factory('App\Models\User')->create()->id,
            'body' => 'Some reply here'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    function test_a_user_can_mark_a_notification_as_read()
    {
        $thread = factory('App\Models\Thread')->create()->subscribe();

        // creating reply for authenticated user
        $thread->addReply([
            'user_id' => factory('App\Models\User')->create()->id,
            'body' => 'Some reply here'
        ]);

        $user = auth()->user();

        $this->assertCount(1, $user->unreadNotifications);

        $notificationId = $user->unreadNotifications->first()->id;

        // if we submit delete to the endpoint to delete notifications
        $this->delete("/profiles/{$user->name}/notifications/{$notificationId}");

        // there should be no notifications left
        $this->assertCount(0, $user->fresh()->unreadNotifications);

    }

    /** @test */
    function test_a_user_can_fetch_their_unread_notifications()
    {
        $thread = factory('App\Models\Thread')->create()->subscribe();

        // creating reply for authenticated user
        $thread->addReply([
            'user_id' => factory('App\Models\User')->create()->id,
            'body' => 'Some reply here'
        ]);

        $user = auth()->user();

        // if we submit delete to the endpoint to delete notifications
        $response = $this->getJson("/profiles/{$user->name}/notifications")->json();

        $this->assertCount(1, $response);
    }
}
