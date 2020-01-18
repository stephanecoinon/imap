<?php

namespace Tests;

use Dotenv\Dotenv;
use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadDotEnv();
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    public function loadDotEnv()
    {
        $path = __DIR__.'/..';

        if (file_exists("$path/.env")) {
            (new Dotenv($path))->load();
        }
    }

    public function pass()
    {
        $this->assertTrue(true);
    }
}
