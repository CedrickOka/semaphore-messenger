<?php
namespace Oka\Messenger\Transport\Semaphore\Tests;

use Oka\Messenger\Transport\Semaphore\Connection;
use Oka\Messenger\Transport\Semaphore\SemaphoreTransport;
use Oka\Messenger\Transport\Semaphore\SemaphoreTransportFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class SemaphoreTransportFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (false === \extension_loaded('sysvmsg')) {
            $this->markTestSkipped('Semaphore extension (sysvmsg) is required.');
        }
    }

    public function testSupportsOnlySemaphoreTransports()
    {
        $factory = new SemaphoreTransportFactory();

        $this->assertTrue($factory->supports('semaphore://localhost', []));
        $this->assertFalse($factory->supports('sqs://localhost', []));
        $this->assertFalse($factory->supports('invalid-dsn', []));
    }

    public function testItCreatesTheTransport()
    {
        $factory = new SemaphoreTransportFactory();
        $serializer = $this->createMock(SerializerInterface::class);

        $expectedTransport = new SemaphoreTransport(Connection::fromDsn('semaphore:///.env', ['foo' => 'bar']), $serializer);

        $this->assertEquals($expectedTransport, $factory->createTransport('semaphore:///.env', ['foo' => 'bar'], $serializer));
    }
}
