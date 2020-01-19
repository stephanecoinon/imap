<?php

namespace StephaneCoinon\Imap;

use Exception;
use StephaneCoinon\Imap\Command;
use StephaneCoinon\Imap\Exceptions\ConnectionException;
use StephaneCoinon\Imap\Exceptions\LoginFailed;
use StephaneCoinon\Imap\Mailbox;
use StephaneCoinon\Imap\Response;
use StephaneCoinon\Imap\Socket;

/**
 * @see INTERNET MESSAGE ACCESS PROTOCOL - VERSION 4rev1 <https://tools.ietf.org/html/rfc3501#page-24>
 */
class Connection
{
    const SSL_PORT = 993;

    /**
     * Server host name.
     *
     * @var string
     */
    protected $host;

    /**
     * Server port.
     *
     * @var int
     */
    protected $port;

    /**
     * Connection socket.
     *
     * @var null|\StephaneCoinon\Imap\Socket
     */
    protected $socket;

    /**
     * SSL options for socket.
     *
     * @var array
     */
    protected $sslOptions = [];

    /**
     * IMAP command tag.
     *
     * It is a unique incremental identifier for each IMAP command sent to the server.
     *
     * @var int
     */
    protected $commandTag = 1;

    /**
     * Response received from last command sent.
     *
     * @var \StephaneCoinon\Imap\Response
     */
    protected $lastResponse;

    /**
     * Make a new Connection instance.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host, int $port = 993)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Get a new instance and open it.
     *
     * @return self
     */
    public static function create(): self
    {
        return (new static(...func_get_args()))->open();
    }

    /**
     * Open the connection.
     *
     * @return self
     * @throws \StephaneCoinon\Imap\Exceptions\ConnectionException if connection fails
     */
    public function open(): self
    {
        if ($this->socket) {
            return $this;
        }

        try {
            $this->createSocket()->open();
        } catch (Exception $e) {
            throw ConnectionException::make('Invalid host', $e);
        }

        return $this;
    }

    /**
     * Create a new socket.
     *
     * @return Socket
     * @throws ConnectionException
     */
    public function createSocket()
    {
        $this->socket = new Socket($this->host, $this->port);

        if ($this->port == static::SSL_PORT) {
            $this->socket->tls($this->sslOptions);
        }

        return $this->socket;
    }

    /**
     * Set the connection socket.
     *
     * @param Socket $socket
     * @return self
     */
    public function withSocket(Socket $socket): self
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * Do not verify the SSL certificate.
     *
     * @return self
     */
    public function doNotVerifySslCert(): self
    {
        $this->sslOptions['verify_peer'] = false;
        $this->sslOptions['verify_peer_name'] = false;

        return $this;
    }

    /**
     * Get connection socket.
     *
     * @return \Socket\Raw\Socket
     */
    public function getSocket(): Socket
    {
        return $this->socket;
    }

    /**
     * Close the socket connection to the IMAP server.
     *
     * @return void
     */
    public function close(): void
    {
        if ($this->socket) {
            $this->socket->close();
            $this->socket = null;
        }
    }

    /**
     * Is the connection open?
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return ! is_null($this->socket);
    }

    /**
     * Is the connection closed?
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Send an IMAP command to the server.
     *
     * @param  string|\StephaneCoinon\Imap\Command $command
     * @return \StephaneCoinon\Imap\Response
     */
    public function command($command): Response
    {
        $commandString = $command instanceof Command ? $command->build() : $command;

        $tag = sprintf('%08d', $this->commandTag);
        $imapCommand = "{$tag} {$commandString}\r\n";
        $lines = [];

        $this->socket->write($imapCommand);

        while ($line = $this->socket->read()) {
            $lines[] = $line;
            if (substr($line, 0, strlen($tag)) == $tag) {
                break;
            }
        }

        $this->lastResponse = new Response($tag, $lines);
        $this->commandTag++;

        return $this->lastResponse;
    }

    /**
     * Get the response received from the last command sent.
     *
     * @return \StephaneCoinon\Imap\Response
     */
    public function lastResponse(): Response
    {
        return $this->lastResponse;
    }

    /**
     * Login using credentials.
     *
     * @param  string $login
     * @param  string $password
     * @return self
     * @throws \StephaneCoinon\Imap\Exceptions\LoginFailed
     */
    public function login(string $login, string $password): self
    {
        $response = $this->command("LOGIN $login $password");

        if ($response->isNotOk()) {
            throw new LoginFailed($response);
        }

        return $this;
    }

    /**
     * Logout and close the connection.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->command('LOGOUT');

        $this->close();
    }

    /**
     * Get a mailbox by name.
     *
     * @param  string $mailboxName
     * @return null|\StephaneCoinon\Imap\Mailbox
     */
    public function mailbox($mailboxName)
    {
        $response = $this->command("SELECT {$mailboxName}");

        return $response->isOk() ? new Mailbox($mailboxName, $this) : null;
    }

    /**
     * Get the default inbox.
     *
     * @return null|\StephaneCoinon\Imap\Mailbox
     */
    public function inbox()
    {
        return $this->mailbox(Mailbox::INBOX);
    }
}
