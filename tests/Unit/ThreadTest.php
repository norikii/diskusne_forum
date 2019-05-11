<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;


class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var
     */
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /**
     * @test
     */
    function test_a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /**
     * @test
     */
    function test_a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->owner);
    }

    /**
     * @test
     */
    function test_a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
           'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /**
     * @test
     */
    function test_a_thread_belongs_to_a_channel()
    {
        $thread = factory('App\Thread')->create();

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /**
     * @test
     */
    function test_a_thread_can_make_a_string_path()
    {
        $thread = factory('App\Thread')->create();

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}", $thread->path());
    }

    /** @test */
    function test_a_thread_can_be_subsribed_to()
    {
        $thread = factory('App\Thread')->create();

        // the user subscribes to the thread
        $thread->subscribe($userId = 1);

        // then we should be able to fetch all threads that the user has subscribed to
        $this->assertEquals(1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /** @test */
    function test_a_thread_can_be_unsubscribed_from()
    {
        $thread = factory('App\Thread')->create();

        // the user subscribes to the thread
        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertNull($thread->subscribtions);
    }

    /** @test */
    function test_it_knows_if_authenticated_user_is_subscribed_to_it()
    {
        $thread = factory('App\Thread')->create();

        $this->be(factory('App\User')->create());

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    function test_a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        // fake replaces the underlying instance of the facade with a fake version = class for the purposes of testing
        // that implements api you need
        // we are faking the notification
        Notification::fake();

        $this->be(factory('App\User')->create());

        $this->thread->subscribe()->addReply([
           'body' => 'Foobar',
           'user_id' => 1
        ]);

        // assert that the user was sent a notification
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }
}
