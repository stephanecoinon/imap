<?php

namespace StephaneCoinon\Imap;

use StephaneCoinon\Imap\Response;

class SearchResponse
{
    /**
     * Base response.
     *
     * @var \StephaneCoinon\Imap\Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the message uids returned by a search command.
     *
     * @return array
     */
    public function uids(): array
    {
        return explode(' ', trim(substr($this->response->line(0), strlen('* SEARCH '))));
    }
}