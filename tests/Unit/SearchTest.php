<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Search;
use Tests\Support\Factories\ResponseFactory;
use Tests\Support\Mocks\MailboxMock;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /** @test */
    function command_is_built_with_ALL_search_key_by_default()
    {
        $this->assertEquals('SEARCH ALL', (new Search)->build());
    }

    /** @test */
    function searching_for_all_messages()
    {
        $this->assertEquals('SEARCH ALL', (new Search)->all()->build());
    }

    /** @test */
    function ALL_search_key_overrides_any_other_key()
    {
        // When building a search using a key
        $search = (new Search)->setKey('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $search->keys());

        // Then specifying the ALL key
        $search->all();

        // Overrides the keys initially set
        $this->assertEquals(['ALL'], $search->keys());
    }

    /** @test */
    function searching_for_messages_and_getting_uids()
    {
        $mailbox = MailboxMock::make(function ($connection) {
            $connection->shouldReceive('command')
                ->andReturn((new ResponseFactory)->makeSearch('1 2 3'));
        });
        $search = new Search($mailbox);

        $uids = $search->findUids();

        $this->assertEquals([1, 2, 3], $uids);
    }

    /** @test */
    function searching_for_a_substring_in_from_field()
    {
        $search = (new Search)->from('joe@example.com');

        $this->assertEquals('SEARCH FROM "joe@example.com"', $search->build());
    }
}
