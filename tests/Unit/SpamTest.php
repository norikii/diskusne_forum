<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;


class SpamTest extends TestCase
{
    /** @test */
    function test_it_checks_for_invalid_keywords()
    {
        // invalid keywords

        // key held down

        $spam = new Spam();

        $this->assertFalse($spam->detect('Spam resistant reply'));

        $this->expectException('Exception');

        $spam->detect('yahoo customer support');
    }

    /** @test */
    function test_checks_for_any_keyword_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('Hello world aaaaaaaaaaaaa');
    }
}
