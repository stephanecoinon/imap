<?php

namespace Tests\Support\Traits;

use Mockery;
use StephaneCoinon\Imap\Connection;

trait MocksConnection
{
    function mockConnection(callable $expectationCallback)
    {
        return Mockery::mock(Connection::class, function ($mock) use ($expectationCallback) {
            $expectationCallback($mock);
        })->makePartial();
    }
}
