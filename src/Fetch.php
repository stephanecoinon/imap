<?php

namespace StephaneCoinon\Imap;

use StephaneCoinon\Imap\Command;

/**
 * Builder for IMAP "FETCH" command.
 *
 * @see https://tools.ietf.org/html/rfc3501#section-6.4.5
 */
class Fetch extends Command
{
    /**
     * Data items to fetch as defined in RFC 3501.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Message UIDs to fetch.
     *
     * @var array
     */
    protected $uids = [];

    /**
     * Set a data item to fetch.
     *
     * @param  string $name
     * @param  mixed $value
     * @return self
     */
    public function setItem(string $name, $value = null): self
    {
        if ($value) {
            $this->items[$name] = $value;
        } else {
            $this->items[] = $name;
        }

        return $this;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function flags(): self
    {
        return $this->setItem('FLAGS');
    }

    public function headers(): self
    {
        return $this->setItem('HEADER');
    }

    public function body(string $section = null, string $partial = null): self
    {
        return $this->setItem(
            'BODY[' . ($section ?? '') . ']' . ($partial ? '<<' . $partial . '>>' : '')
        );
    }

    /**
     * Set or get the uids.
     *
     * @param array $uids
     * @return $this|array
     */
    public function uids(array $uids = [])
    {
        // Return the uids when method is called without an argument
        if ($uids == []) {
            return $this->uids;
        }

        // Set the uids otherwise
        $this->uids = $uids;

        return $this;
    }

    public function uid(int $uid): self
    {
        return $this->uids([$uid]);
    }

    protected function buildUids(): string
    {
        return implode(',', $this->uids);
    }

    protected function buildItems(): string
    {
        return implode(' ', $this->items);
    }

    /**
     * Build the command.
     *
     * @return string
     */
    public function build(): string
    {
        $items = $this->buildItems();

        return 'FETCH ' . $this->buildUids()
            . ($items ? ' (' . $items . ')' : '');
    }
}
