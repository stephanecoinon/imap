<?php

namespace StephaneCoinon\Imap;

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
     * Search for messages in this mailbox and get their UIDs.
     *
     * @param  string|\StephaneCoinon\Imap\Search $criteria
     * @return string[] UIDs of the messages matching $criteria
     */
    public function searchAndReturnUids($criteria)
    {
        $command = $criteria instanceof Search
        ? $criteria
        : Search::createFromCriteria($criteria);

        return (new SearchResponse($this->command($command)))->uids();
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
     * @param  string|\StephaneCoinon\Imap\Search $criteria search criteria
     * @param  null|\StephaneCoinon\Imap\Fetch $fetch message fetch options
     * @return Message[]
     */
    public function search($criteria, Fetch $fetch = null)
    {
        $fetch or $fetch = Search::defaultFetchOptions();

        // Fetch the message uids
        $uids = $this->searchAndReturnUids($criteria);

        // Fetch the messages
        return Message::createCollectionFromResponse(
            $this->command($fetch->uids($uids))
        );
    }
}
