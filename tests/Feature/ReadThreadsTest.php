<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    private $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /**
     * @test
     */
    public function test_a_user_can_view_all_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    public function test_a_user_can_view_a_single_thread()
    {
        $response = $this->get('/threads/channel1/' . $this->thread->id);
        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    function test_a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = factory('App\Channel')->create();
        $threadInChannel = factory('App\Thread')->create(['channel_id' => $channel->id]);
        $threadNotInChannel = factory('App\Thread')->create();

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function test_a_user_can_filter_threads_by_any_username()
    {
        $this->actingAs(factory('App\User')->create(['name' => 'Norik']));

        $threadByNorik = factory('App\Thread')->create(['user_id' => auth()->id()]);
        $threadNotByNorik = factory('App\Thread')->create();

        $this->get('threads?by=Norik')
            ->assertSee($threadByNorik->title)
            ->assertDontSee($threadNotByNorik->title);
    }

    /** @test  */
    function test_a_user_can_filter_threads_by_popularity()
    {
        $threadsWithTwoReplies = factory('App\Thread')->create();
        factory('App\Reply', 2)->create(['thread_id' => $threadsWithTwoReplies]);

        $threadsWithThreeReplies = factory('App\Thread')->create();
        factory('App\Reply', 3)->create(['thread_id' => $threadsWithThreeReplies]);

        $threadsWithNoReplies = $this->thread;

        $response = $this->get('threads?popular=1');

        // assert the number of responses for a thread are in this order
        $response->assertSeeInOrder([
            $threadsWithThreeReplies->title,
            $threadsWithTwoReplies->title,
            $threadsWithNoReplies->title
        ]);
    }

    /** @test */
    function test_a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = factory('App\Thread')->create();
        factory('App\Reply')->create(['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response);
    }


    /** @test */
    function test_a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = factory('App\Thread')->create();
        factory('App\Reply', 2)->create(['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }
}
