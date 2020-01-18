<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Response;
use StephaneCoinon\Imap\SearchResponse;
use Tests\TestCase;

class SearchResponseTest extends TestCase
{
    /** @test */
    function getting_message_uids()
    {
        $response = new SearchResponse(new Response('00000001', [
            "* SEARCH 1 2 3\r\n",
            "00000001 OK Search completed (0.001 + 0.000 secs).\r\n"
        ]));

        $this->assertEquals([1, 2, 3], $response->uids());
    }
}