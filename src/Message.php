<?php

namespace StephaneCoinon\Imap;

use StephaneCoinon\Imap\Response;

class Message
{
    /**
     * IMAP response the message originates from.
     *
     * @var \StephaneCoinon\Imap\Response
     */
    protected $response;

    public static function createCollectionFromResponse(Response $collectionResponse)
    {
        return array_map(function ($response) {
            return static::createFromResponse($response);
        }, $collectionResponse->explode());
    }

    public static function createFromResponse(Response $response): self
    {
        $message = new static;
        $message->response = $response;

        return $message;
    }

    public function raw(): string
    {
        $lines = $this->response->lines();
        $lineCount = count($lines);

        if ($lineCount < 4) {
            return '';
        }

        // Skip the first line ('* n FETCH...')
        // and the last 2 lines (')' and 'tag OK Fetch...')
        return implode(array_slice($lines, 1, $lineCount - 3));
    }
}
