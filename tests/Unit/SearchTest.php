<?php

namespace Tests\Unit;

use StephaneCoinon\Imap\Search;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /** @test */
    function command_is_built_with_ALL_search_key_by_default()
    {
        $this->assertEquals('SEARCH ALL', (new Search)->build());
    }

    /** @test */
    function building_command_from_raw_search_criteria()
    {
        $search = Search::createFromCriteria(
            $criteria = 'FLAGGED SINCE 1-Feb-1994 NOT FROM "Smith"'
        );

        $this->assertEquals("SEARCH $criteria", $search->build());
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
}