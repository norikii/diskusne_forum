<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /**
     * @test
     */
    function test_an_authenticated_user_can_create_new_forum_thread()
    {
        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();

        $this->post('/threads', $thread->toArray());

        $this->get($thread->path())
             ->assertSee($thread->title)
             ->assertSee($thread->body);
    }

    /**
     * @test
     */
    function test_guest_cannot_see_the_create_thread_page()
    {
        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    function test_a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    function test_a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    function test_a_thread_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /**
     * @param array $overrides
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->be(factory('App\User')->create());

        $thread = factory('App\Thread')->make($overrides);

        return $this->post('/threads', $thread->toArray());
    }

    /** @test */
    function test_an_authorized_user_cannot_delete_threads_of_others()
    {
        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create(['user_id' => auth()->id()]);
        $reply = factory('App\Reply')->create(['thread_id' => $thread->id, 'user_id' => auth()->id()]);

        $response = $this->json('DELETE', $thread->path());
        $response->assertStatus(403);

        $response->assertSee($thread->id);
        $response->assertSee($reply->id);
    }

    /** @test */
    function test_guest_cannot_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = factory('App\Thread')->create();

        $response = $this->delete($thread->path());

        $response->assertRedirect('/login');
    }

    /** @test */
    function test_unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = factory('App\Thread')->create();

        $this->be(factory('App\User')->create());
        $this->delete($thread->path())
            ->assertStatus(403);
    }
}
