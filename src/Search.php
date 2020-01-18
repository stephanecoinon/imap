<?php

namespace StephaneCoinon\Imap;

use StephaneCoinon\Imap\Command;
use StephaneCoinon\Imap\Fetch;

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
    protected $criteria;

    /**
     * Set the raw criteria to use to build the search command.
     *
     * @param string $criteria
     * @return self
     */
    public static function createFromCriteria(string $criteria): self
    {
        $search = new static;
        $search->criteria = $criteria;

        return $search;
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
}
