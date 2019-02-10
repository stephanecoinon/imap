<?php

namespace StephaneCoinon\Imap\Exceptions;

use Exception;
use StephaneCoinon\Imap\Response;

class ImapException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if ($message instanceof Response) {
            $message = $message->error();
        }

        parent::__construct($message, $code, $previous);
    }
}
