<?php

namespace Tests\Support\Mocks;

use Closure;
use StephaneCoinon\Imap\Mailbox;
use Tests\Support\Mocks\ConnectionMock;

class MailboxMock extends Mailbox
{
    public static function make(Closure $expectationCallback)
    {
        return Mailbox::inbox(ConnectionMock::make($expectationCallback));
    }
}
