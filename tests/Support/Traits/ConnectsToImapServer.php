<?php

namespace Tests\Support\Traits;

use StephaneCoinon\Imap\Connection;

trait ConnectsToImapServer
{
    /**
     * IMAP connection
     *
     * @var \StephaneCoinon\Imap\Connection
     */
    protected $imap = null;

    public function tearDown()
    {
        if ($this->imap) {
            $this->imap->close();
        }
    }

    /**
     * Connection to the test IMAP server.
     */
    function connect()
    {
        $this->imap = (new Connection(getenv('IMAP_HOST'), getenv('IMAP_PORT')))
            ->doNotVerifySslCert()
            ->open();

        return $this->imap;
    }

    /**
     * Log into the test IMAP server.
     */
    function login($username = null, $password = null)
    {
        return $this->connect()
            ->login(
                $username ?: getenv('IMAP_USERNAME'),
                $password ?: getenv('IMAP_PASSWORD')
            );
    }
}