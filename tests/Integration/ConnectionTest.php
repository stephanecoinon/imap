<?php

namespace Tests\Integration;

use Exception;
use StephaneCoinon\Imap\Connection;
use StephaneCoinon\Imap\Exceptions\LoginFailed;
use StephaneCoinon\Imap\Mailbox;
use Tests\Support\Traits\ConnectsToImapServer;
use Tests\TestCase;

class ConnectionTest extends TestCase
{
    use ConnectsToImapServer;

    /** @test */
    function it_can_connect_to_a_host()
    {
        $this->connect();

        $this->assertTrue($this->imap->isOpen());
    }

    /** @test */
    function it_throws_an_exception_when_host_name_is_not_valid()
    {
        try {
            $imap = (new Connection('incorrect-server-name'))->open();
        } catch (\Exception $e) {
            $this->assertStringStartsWith($e->getMessage(), 'Invalid host');
            return;
        }

        $this->fail('An exception was not thrown even though an invalid server name was passed');
    }

    /** @test */
    function it_can_login()
    {
        $imap = $this->connect();

        $imap->login(getenv('IMAP_USERNAME'), getenv('IMAP_PASSWORD'));

        $this->pass();
    }

    /** @test */
    function it_throws_an_exception_when_login_fails()
    {
        $imap = $this->connect();

        try {
            $imap->login('incorrect@example.com', 'incorrrect-password');
        } catch (LoginFailed $e) {
            $this->pass();
            return;
        } catch (Exception $e) {
        }

        $this->fail('LoginFailed exception was not thrown even though invalid credentials were used to login');
    }

    /** @test */
    function it_can_logout()
    {
        $imap = $this->login();

        $imap->logout();

        $this->assertStringStartsWith('* BYE', $imap->lastResponse()->lines()[0]);
        $this->assertTrue($imap->isClosed());
    }

    /** @test */
    function it_can_select_a_mailbox()
    {
        $imap = $this->login();

        $mailbox = $imap->mailbox($name = 'INBOX');

        $this->assertInstanceOf(Mailbox::class, $mailbox);
        $this->assertSame($name, $mailbox->name());
        $this->assertSame($imap, $mailbox->connection());
    }
}