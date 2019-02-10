<?php

namespace StephaneCoinon\Imap\Exceptions;

use Exception;

class ConnectionException extends Exception
{
    public static function make($message, $previous = null)
    {
        return new static($message, $previous ? $previous->getCode() : 0, $previous);
    }
}
