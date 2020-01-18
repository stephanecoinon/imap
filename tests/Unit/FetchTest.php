<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Fetch;
use Tests\TestCase;

class FetchTest extends TestCase
{
    /** @test */
    function uid_only()
    {
        $this->assertEquals('FETCH 1', (new Fetch)->uid(1)->build());
    }

    /** @test */
    function flags()
    {
        $this->assertEquals('FETCH 1 (FLAGS)', (new Fetch)->uid(1)->flags()->build());
    }

    /** @test */
    function body()
    {
        $this->assertEquals('FETCH 1 (BODY[])', (new Fetch)->uid(1)->body()->build());
    }

    /** @test */
    function flags_and_body()
    {
        $this->assertEquals('FETCH 1 (FLAGS BODY[])', (new Fetch)->uid(1)->flags()->body()->build());
    }
}