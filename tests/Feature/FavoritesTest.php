<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function test_an_authenticated_user_can_favorite_any_reply()
    {
        $this->be(factory('App\User')->create());

        $reply = factory('App\Reply')->create();

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    function test_guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')
            ->assertRedirect('/login');
    }

    /** @test */
    function test_an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->be(factory('App\User')->create());

        $reply = factory('App\Reply')->create();

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $exception) {
            $this->fail('Did not expect to insert the same record twice');
        }

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    function test_an_authenticated_user_can_unfavorite_any_reply()
    {
        $this->be(factory('App\User')->create());

        $reply = factory('App\Reply')->create();

        $reply->favorite();

        $this->delete('replies/' . $reply->id . '/favorites');

        $this->assertCount(0, $reply->fresh()->favorites);
    }
}
