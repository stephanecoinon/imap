<?php

namespace Tests\Unit;

use Mockery;
use StephaneCoinon\Imap\Command;
use StephaneCoinon\Imap\Connection;
use StephaneCoinon\Imap\Socket;
use Tests\Support\Mocks\ConnectionMock;
use Tests\TestCase;

class ConnectionTest extends TestCase
{
    /** @test */
    function sending_string_command()
    {
        $connection = ConnectionMock::make(function ($connection) {
            $connection->shouldReceive('command')
                ->with('DUMMY COMMAND');
        });

        $connection->command('DUMMY COMMAND');

        $this->pass();
    }

    /** @test */
    function sending_command_instance()
    {
        $socket = Mockery::mock(Socket::class, function ($socket) {
            $socket->shouldReceive('write')
                ->with("00000001 COMMAND STUB\r\n");
            $socket->shouldReceive('read')->andReturn('');
        })->makePartial();
        $connection = (new Connection('host'))->withSocket($socket);

        $connection->command(new CommandStub);

        $this->pass();
    }
}


class CommandStub extends Command
{
    public function build(): string
    {
        return 'COMMAND STUB';
    }
}
