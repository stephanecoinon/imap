<?php

namespace StephaneCoinon\Imap;

class Response
{
    /**
     * Formatted IMAP command tag.
     *
     * @var string
     */
    protected $tag;

    /**
     * Lines returned by IMAP server in the response.
     *
     * @var string[]
     */
    protected $lines = [];

    public function __construct(string $tag, array $lines = [])
    {
        $this->tag = $tag;
        $this->lines = $lines;
    }

    public function lastLine(): string
    {
        $lineCount = count($this->lines);

        return $lineCount > 0 ? $this->lines[$lineCount - 1] : '';
    }

    public function isOk(): bool
    {
        $ok = "{$this->tag} OK";

        return substr($this->lastLine(), 0, strlen($ok)) == $ok;
    }

    public function isNotOk(): bool
    {
        return ! $this->isOk();
    }

    public function lines(): array
    {
        return $this->lines;
    }

    /**
     * Get the error from the last IMAP response.
     *
     * @return string
     */
    public function error()
    {
        return $this->lastLine();
    }
}