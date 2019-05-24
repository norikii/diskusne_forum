<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function test_a_user_has_a_profile()
    {
        $user = factory('App\Models\User')->create();

        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }

    /** @test */
    function test_profiles_display_all_threads_created_by_the_associated_user()
    {
        $this->be(factory('App\Models\User')->create());

        $thread = factory('App\Models\Thread')->create(['user_id' => auth()->id()]);

        $this->get("/profiles/" . auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

}
