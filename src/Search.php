<?php

namespace StephaneCoinon\Imap;

use StephaneCoinon\Imap\Command;
use StephaneCoinon\Imap\Fetch;
use StephaneCoinon\Imap\SearchResponse;

/**
 * Builder for IMAP "SEARCH" command.
 *
 * @see https://tools.ietf.org/html/rfc3501#section-6.4.4
 */
class Search extends Command
{
    /**
     * Search keys as defined in RFC 3501.
     *
     * @var array
     */
    protected $keys = [];

    /**
     * Criteria set when building command with Search::createFromCriteria().
     *
     * @var string
     */
    protected $criteria = '';

    /**
     * Mailbox to search in.
     *
     * @var \StephaneCoinon\Imap\Mailbox
     */
    protected $mailbox;

    /**
     * Message fetch options.
     *
     * @var \StephaneCoinon\Imap\Fetch
     */
    protected $fetch;

    public function __construct(Mailbox $mailbox = null, Fetch $fetch = null)
    {
        $this->mailbox = $mailbox;
        $this->fetch = $fetch ?: static::defaultFetchOptions();
    }

    /**
     * Get a new Search instance connected to a mailbox.
     *
     * @param  Mailbox $mailbox
     * @param  null|Fetch $fetch message fetch options
     * @return static
     */
    public static function inMailbox(Mailbox $mailbox, Fetch $fetch = null)
    {
        return new static($mailbox, $fetch);
    }

    public function getCriteria(): string
    {
        return $this->criteria;
    }

    /**
     * Get default fetch options for search.
     *
     * @return \StephaneCoinon\Imap\Fetch
     */
    public static function defaultFetchOptions(): Fetch
    {
        return (new Fetch)->flags()->body();
    }

    /**
     * Set a search key.
     *
     * @param  string $name
     * @param  mixed $value
     * @return self
     */
    public function setKey(string $name, $value): self
    {
        $this->keys[$name] = $value;

        return $this;
    }

    public function keys(): array
    {
        return $this->keys;
    }

    /**
     * Match messages with a substring in the From: field
     *
     * @param  string $from
     * @return $this
     */
    public function from(string $from)
    {
        $this->criteria = sprintf('FROM "%s"', $from);

        return $this;
    }

    /**
     * Get all messages in the mailbox.
     *
     * @return self
     */
    public function all(): self
    {
        // 'ALL' overrides all other keys
        $this->keys = ['ALL'];

        return $this;
    }

    /**
     * Build the command.
     *
     * @return string
     */
    public function build(): string
    {
        // Build command using raw criteria if set.
        if ($this->criteria) {
            return "SEARCH {$this->criteria}";
        }

        // Search for all messages by default, if no keys are specified.
        if ($this->keys == [] || $this->keys == ['ALL']) {
            return 'SEARCH ALL';
        }
    }


    /**
     * Search for messages and get their UIDs.
     *
     * @return string[] UIDs of the messages matching this search
     */
    public function findUids()
    {
        return (new SearchResponse($this->mailbox->command($this)))->uids();
    }

    /**
     * Run the search and return the messages found.
     *
     * @param  null|string $criteria raw IMAP criteria
     * @return Message[]
     */
    public function get($criteria = null)
    {
        if ($criteria) {
            $this->criteria = $criteria;
        }

        // Fetch the message uids
        $uids = $this->findUids();

        // Fetch the messages
        return Message::createCollectionFromResponse(
            $this->mailbox->command($this->fetch->uids($uids))
        );
    }
}
