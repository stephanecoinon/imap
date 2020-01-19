<?php

namespace Tests\Support\Factories;

use StephaneCoinon\Imap\Response;

class ResponseFactory
{
    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * IMAP command tag.
     *
     * @var integer
     */
    protected $tag = 1;

    public function __construct()
    {
        $this->messageFactory = new MessageFactory;
    }

    function make(array $messageParts = [])
    {
        ! $messageParts and $messageParts = $this->messageFactory->makePartAsArray();

        return new Response(
            $tag = sprintf('%8d', $this->tag++),
            $messageParts + [
                "{$tag} OK Fetch completed (0.002 + 0.000 + 0.001 secs).\r\n"
            ]
        );
    }

    function makeSearch($response) {
        return new Response($tag = '00000003', [
            "* SEARCH {$response}\r\n",
            "$tag OK Search completed (0.001 + 0.000 secs).\r\n"
        ]);
    }
}
