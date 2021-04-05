<?php

namespace Oka\Messenger\Transport\Semaphore\Tests;

use Oka\Messenger\Transport\Semaphore\Connection;
use Oka\Messenger\Transport\Semaphore\SemaphoreEnvelope;
use Oka\Messenger\Transport\Semaphore\SemaphoreReceiver;
use Oka\Messenger\Transport\Semaphore\Tests\Fixtures\DummyMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer as SerializerComponent;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class SemaphoreReceiverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (false === \extension_loaded('sysvmsg')) {
            self::markTestSkipped('Semaphore extension (sysvmsg) is required.');
        }
    }

    public function testItReturnsTheDecodedMessageToTheHandler(): void
    {
        $serializer = new Serializer(
            new SerializerComponent\Serializer([new ObjectNormalizer()], ['json' => new JsonEncoder()])
        );

        $semaphoreEnvelope = $this->createSemaphoreEnvelope();
        $connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $connection->method('get')->willReturn($semaphoreEnvelope);

        $receiver = new SemaphoreReceiver($connection, $serializer);
        $actualEnvelopes = iterator_to_array($receiver->get());

        self::assertCount(1, $actualEnvelopes);
        self::assertEquals(new DummyMessage('Hi'), $actualEnvelopes[0]->getMessage());
    }

    private function createSemaphoreEnvelope(): SemaphoreEnvelope
    {
        $envelope = $this->getMockBuilder(SemaphoreEnvelope::class)->disableOriginalConstructor()->getMock();
        $envelope->method('getBody')->willReturn('{"message": "Hi"}');
        $envelope->method('getHeaders')->willReturn(
            [
                'type' => DummyMessage::class,
            ]
        );

        return $envelope;
    }
}
