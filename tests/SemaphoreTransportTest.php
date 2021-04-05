<?php

namespace Oka\Messenger\Transport\Semaphore\Tests;

use Oka\Messenger\Transport\Semaphore\Connection;
use Oka\Messenger\Transport\Semaphore\SemaphoreEnvelope;
use Oka\Messenger\Transport\Semaphore\SemaphoreTransport;
use Oka\Messenger\Transport\Semaphore\Tests\Fixtures\DummyMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class SemaphoreTransportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (false === \extension_loaded('sysvmsg')) {
            self::markTestSkipped('Semaphore extension (sysvmsg) is required.');
        }
    }

    public function testItIsATransport(): void
    {
        $transport = $this->getTransport();

        self::assertInstanceOf(TransportInterface::class, $transport);
    }

    public function testReceivesMessages(): void
    {
        $transport = $this->getTransport(
            $serializer = $this->createMock(SerializerInterface::class),
            $connection = $this->createMock(Connection::class)
        );

        $decodedMessage = new DummyMessage('Decoded.');

        $semaphoreEnvelope = $this->getMockBuilder(SemaphoreEnvelope::class)->disableOriginalConstructor()->getMock();
        $semaphoreEnvelope->method('getBody')->willReturn('body');
        $semaphoreEnvelope->method('getHeaders')->willReturn(['my' => 'header']);

        $serializer->method('decode')->with(['body' => 'body', 'headers' => ['my' => 'header']])->willReturn(
            new Envelope($decodedMessage)
        );
        $connection->method('get')->willReturn($semaphoreEnvelope);

        $envelopes = iterator_to_array($transport->get());

        self::assertSame($decodedMessage, $envelopes[0]->getMessage());
    }

    private function getTransport(
        SerializerInterface $serializer = null,
        Connection $connection = null
    ): SemaphoreTransport {
        $serializer = $serializer ?: $this->createMock(SerializerInterface::class);
        $connection = $connection ?: $this->createMock(Connection::class);

        return new SemaphoreTransport($connection, $serializer);
    }
}
