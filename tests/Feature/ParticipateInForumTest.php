<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        // gives as an authenticated user
        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make();
        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /**
     * @test
     */
    function test_an_unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling()
            ->post('threads/channel1/1/replies', [])
        ->assertRedirect('/login');
    }

    /**
     * @test
     */
    function test_a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make(['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
                ->assertSessionHasErrors('body');
    }

    /** @test */
    function test_unauthorized_user_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = factory('App\Reply')->create();

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->be(factory('App\User')->create())
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    function test_authorized_users_can_delete_replies()
    {
        $this->be(factory('App\User')->create());

        $reply = factory('App\Reply')->create(['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    function test_authorized_users_can_update_replies()
    {
        $this->be(factory('App\User')->create());

        $reply = factory('App\Reply')->create(['user_id' => auth()->id()]);

        $updatedReply = 'You have been changed';
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }

    /** @test */
    function test_unauthorized_user_cannot_update_replies()
    {
        $reply = factory('App\Reply')->create();

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->be(factory('App\User')->create())
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    function test_replies_that_contain_spam_may_not_be_created()
    {
        $this->withoutExceptionHandling();

        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make([
           'body' => 'Yahoo customer support'
        ]);

        $this->expectException(\Exception::class);

        $this->post($thread->path() . '/replies', $reply->toArray());
    }
}
