<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Response;
use Tests\TestCase;

class ResponseTest extends TestCase
{
    /** @test */
    function splitting_a_multi_part_response_into_multiple_responses()
    {
        $multiPartResponse = new Response('1', [
            "* 1 FETCH (BODY[] {2769}\r\n",
            "Subject: test\r\n",
            "\r\n",
            "lorem ipsum\r\n",
            ")\r\n",
            "* 2 FETCH (BODY[] {2770}\r\n",
            "Subject: test 2\r\n",
            "\r\n",
            "lorem ipsum\r\n",
            ")\r\n",
            "00000003 OK Fetch completed (0.002 + 0.000 + 0.001 secs).\r\n",
        ]);

        $responses = $multiPartResponse->explode();

        $this->assertInternalType('array', $responses);
        $this->assertContainsOnlyInstancesOf(Response::class, $responses);
        $this->assertCount(2, $responses);
    }
}
