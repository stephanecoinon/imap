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

    /**
     * Get the inbox.
     *
     * @param  \StephaneCoinon\Imap\Connection $connection
     * @return static
     */
    public static function inbox(Connection $connection)
    {
        return new static(static::INBOX, $connection);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * Run an IMAP command.
     *
     * @param  string|\StephaneCoinon\Imap\Command $command
     * @return \StephaneCoinon\Imap\Response
     */
    public function command($command)
    {
        return $this->connection->command($command);
    }

    /**
     * Fetch a message by uid.
     *
     * @param integer $uid
     * @return \StephaneCoinon\Imap\Message
     */
    public function fetch(int $uid)
    {
        return Message::createFromResponse(
            $this->command((new Fetch)->uid($uid)->body())
        );
    }

    /**
     * Search for messages in this mailbox.
     *
     * @param  null|string|\StephaneCoinon\Imap\Search $criteria search criteria
     * @param  null|\StephaneCoinon\Imap\Fetch $fetch message fetch options
     * @return Search|Message[]
     */
    public function search($criteria = null, Fetch $fetch = null)
    {
        $search = Search::inMailbox($this, $fetch);

        return is_null($criteria) ? $search : $search->get($criteria);
    }
}
