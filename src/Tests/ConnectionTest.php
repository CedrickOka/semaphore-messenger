<?php
namespace Oka\Messenger\Transport\Semaphore\Tests;

use PHPUnit\Framework\TestCase;
use Oka\Messenger\Transport\Semaphore\Connection;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class ConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (false === \extension_loaded('sysvmsg')) {
            $this->markTestSkipped('Semaphore extension (sysvmsg) is required.');
        }
    }

    public function testItCannotBeConstructedWithAWrongDsn()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The given Semaphore Messenger DSN "semaphore://:" is invalid.');
        Connection::fromDsn('semaphore://:');
    }

    public function testItCanBeConstructedWithDefaults()
    {
        $this->assertEquals(
                new Connection([
                    'path' => '/',
                    'project' => 'M',
                    'message_max_size' => 131072,
                ]),
                Connection::fromDsn('semaphore:///')
        );
    }

    public function testOverrideOptionsViaQueryParameters()
    {
        $this->assertEquals(
                new Connection([
                    'path' => '/.env',
                    'project' => 'T',
                    'message_max_size' => 1024,
                ]),
                Connection::fromDsn('semaphore:///.env?project=T&message_max_size=1024')
        );
    }

    public function testOptionsAreTakenIntoAccountAndOverwrittenByDsn()
    {
        $this->assertEquals(
                new Connection([
                    'path' => '/.env',
                    'project' => 'T',
                    'message_max_size' => 1024,
                ]),
                Connection::fromDsn('semaphore:///.env?project=T&message_max_size=1024', [
                    'message_max_size' => 131072,
                ])
        );
    }
}
