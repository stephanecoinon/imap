<?php

namespace StephaneCoinon\Imap;

use StephaneCoinon\Imap\Connection;

class Mailbox
{
    /**
     * Name of the default inbox.
     */
    const INBOX = 'INBOX';

    /**
     * Mailbox name.
     *
     * @var string
     */
    protected $name;

    /**
     * Connection to IMAP server.
     *
     * @var \StephaneCoinon\Imap\Connection
     */
    protected $connection;

    public function __construct(string $name, Connection $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function connection(): Connection
    {
        return $this->connection;
    }
}
