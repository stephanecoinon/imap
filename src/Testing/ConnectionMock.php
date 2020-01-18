<?php

namespace StephaneCoinon\Imap\Testing;

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

    public function stack(array $stack): self
    {
        $this->responseStack = $stack;

        return $this;
    }

    public function command($command)
    {
        return $this->commandId < count($this->responseStack)
            ? $this->responseStack[$this->commandId++]
            : null;
    }
}
