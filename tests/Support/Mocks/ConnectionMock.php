<?php

namespace Tests\Support\Mocks;

use Closure;
use Mockery;
use StephaneCoinon\Imap\Connection;
use StephaneCoinon\Imap\Response;

class ConnectionMock extends Connection
{
    protected $responseStack = [];

    protected $commandId = 0;

    public function __construct($host = null, $port = 993)
    {
        //
    }

    public static function make(Closure $expectationCallback = null)
    {
        return Mockery::mock(Connection::class, function ($mock) use ($expectationCallback) {
            $expectationCallback($mock);
        })->makePartial();
    }

    public function stack(array $stack): self
    {
        $this->responseStack = $stack;

        return $this;
    }

    public function command($command): Response
    {
        return $this->commandId < count($this->responseStack)
            ? $this->responseStack[$this->commandId++]
            : new Response;
    }
}
