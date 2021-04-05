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
            self::markTestSkipped('Semaphore extension (sysvmsg) is required.');
        }
    }

    public function testSupportsOnlySemaphoreTransports(): void
    {
        $factory = new SemaphoreTransportFactory();

        self::assertTrue($factory->supports('semaphore://localhost', []));
        self::assertFalse($factory->supports('sqs://localhost', []));
        self::assertFalse($factory->supports('invalid-dsn', []));
    }

    public function testItCreatesTheTransport(): void
    {
        $factory = new SemaphoreTransportFactory();
        $serializer = $this->createMock(SerializerInterface::class);

        $expectedTransport = new SemaphoreTransport(
            Connection::fromDsn('semaphore:///.env', ['foo' => 'bar']),
            $serializer
        );

        self::assertEquals(
            $expectedTransport,
            $factory->createTransport('semaphore:///.env', ['foo' => 'bar'], $serializer)
        );
    }
}
