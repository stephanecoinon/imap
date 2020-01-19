<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Mailbox;
use StephaneCoinon\Imap\Message;
use StephaneCoinon\Imap\Search;
use Tests\Support\Factories\MessageFactory;
use Tests\Support\Factories\ResponseFactory;
use Tests\Support\Mocks\ConnectionMock;
use Tests\Support\Mocks\MailboxMock;
use Tests\TestCase;

class MailboxTest extends TestCase
{
    /** @test */
    function fetching_a_message_by_uid()
    {
        $mailbox = MailboxMock::make(function ($connection) {
            $connection->shouldReceive('command')
                ->withArgs(function ($fetch) {
                    return $fetch->items() == ['BODY[]']
                        && $fetch->uids() == [1];
                })
                ->andReturn((new ResponseFactory)->make());
        });

        $message = $mailbox->fetch(1);

        $this->assertInstanceOf(Message::class, $message);
    }

    /** @test */
    function search_can_return_one_message()
    {
        $factory = new ResponseFactory;
        $connection = (new ConnectionMock)->stack([
            $factory->makeSearch('1'),
            $factory->make()
        ]);
        $mailbox = Mailbox::inbox($connection);

        $messages = $mailbox->search('SUBJECT test');

        $this->assertInternalType('array', $messages);
        $this->assertCount(1, $messages);
        $this->assertContainsOnlyInstancesOf(Message::class, $messages);
    }

    /** @test */
    function search_can_return_multiple_message()
    {
        $factory = new ResponseFactory;
        $msgFactory = new MessageFactory;
        $connection = (new ConnectionMock)->stack([
            $factory->makeSearch('1'),
            $factory->make(array_merge(
                $msgFactory->makePartAsArray(1),
                $msgFactory->makePartAsArray(2),
                $msgFactory->makePartAsArray(3),
            )),
        ]);
        $mailbox = Mailbox::inbox($connection);

        $messages = $mailbox->search('SUBJECT test');

        $this->assertInternalType('array', $messages);
        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(Message::class, $messages);
    }

    /** @test */
    function getting_search_builder_instance()
    {
        $mailbox = Mailbox::inbox(new ConnectionMock);

        $search = $mailbox->search();

        $this->assertInstanceOf(Search::class, $search);
    }
}
