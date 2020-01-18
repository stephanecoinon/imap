<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Connection;
use StephaneCoinon\Imap\Mailbox;
use StephaneCoinon\Imap\Message;
use StephaneCoinon\Imap\Response;
use Tests\Support\Traits\MocksConnection;
use Tests\TestCase;

class MailboxTest extends TestCase
{
    use MocksConnection;

    protected $tag = 1;

    /** @test */
    function fetching_a_message_by_uid()
    {
        $mailbox = $this->mockMailbox(function ($connection) {
            $connection->shouldReceive('command')
                ->withArgs(function ($fetch) {
                    return $fetch->items() == ['BODY[]']
                        && $fetch->uids() == [1];
                })
                ->andReturn($this->makeResponse());
        });

        $message = $mailbox->fetch(1);

        $this->assertInstanceOf(Message::class, $message);
    }

    /** @test */
    function searching_for_messages_and_getting_uids()
    {
        $mailbox = $this->mockMailbox(function ($connection) {
            $connection->shouldReceive('command')
                ->withArgs(function ($arg) {
                    return $arg->getCriteria() == 'ALL';
                })
                ->andReturn($this->makeSearchResponse('1 2 3'));
        });

        $uids = $mailbox->searchAndReturnUids('ALL');

        $this->assertEquals([1, 2, 3], $uids);
    }

    /** @test */
    function search_can_return_one_message()
    {
        $connection = Connection::mock()->stack([
            $this->makeSearchResponse('1'),
            $this->makeResponse()
        ]);
        $mailbox = $this->makeMailbox($connection);

        $messages = $mailbox->search('SUBJECT test');

        $this->assertInternalType('array', $messages);
        $this->assertCount(1, $messages);
        $this->assertContainsOnlyInstancesOf(Message::class, $messages);
    }

    /** @test */
    function search_can_return_multiple_message()
    {
        $connection = Connection::mock()->stack([
            $this->makeSearchResponse('1'),
            $this->makeResponse(array_merge(
                $this->makeMessagePartAsArray(1),
                $this->makeMessagePartAsArray(2),
                $this->makeMessagePartAsArray(3),
            )),
        ]);
        $mailbox = $this->makeMailbox($connection);

        $messages = $mailbox->search('SUBJECT test');

        $this->assertInternalType('array', $messages);
        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(Message::class, $messages);
    }

    function makeMailbox(Connection $connection)
    {
        return new Mailbox(Mailbox::INBOX, $connection);
    }

    function mockMailbox(callable $expectationCallback)
    {
        return $this->makeMailbox($this->mockConnection($expectationCallback));
    }

    function makeSearchResponse($response) {
        return new Response($tag = '00000003', [
            "* SEARCH {$response}\r\n",
            "$tag OK Search completed (0.001 + 0.000 secs).\r\n"
        ]);
    }

    function makeMessagePartAsArray($id = 1)
    {
        return [
            "* $id FETCH (BODY[] {2769}\r\n",
            "Subject: test\r\n",
            "To: joe@example.com\r\n",
            "Content-Type: multipart/alternative; boundary=\"0000000000009e67480569e8db24\"\r\n",
            "\r\n",
            "--0000000000009e67480569e8db24\r\n",
            "Content-Type: text/plain; charset=\"UTF-8\"\r\n",
            "\r\n",
            "lorem ipsum\r\n",
            "\r\n",
            "--0000000000009e67480569e8db24\r\n",
            "Content-Type: text/html; charset=\"UTF-8\"\r\n",
            "\r\n",
            "<div dir=\"ltr\">lorem ipsum</div>\r\n",
            "\r\n",
            "--0000000000009e67480569e8db24--\r\n",
            ")\r\n",
        ];
    }

    function makeResponse(array $parts = [])
    {
        ! $parts and $parts = $this->makeMessagePartAsArray();

        return new Response(
            $tag = sprintf('%8d', $this->tag++),
            $parts + [
                "{$tag} OK Fetch completed (0.002 + 0.000 + 0.001 secs).\r\n"
            ]
        );
    }
}
