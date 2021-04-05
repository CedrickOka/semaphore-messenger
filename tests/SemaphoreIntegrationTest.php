<?php

namespace Oka\Messenger\Transport\Semaphore\Tests;

use Oka\Messenger\Transport\Semaphore\Connection;
use Oka\Messenger\Transport\Semaphore\SemaphoreReceiver;
use Oka\Messenger\Transport\Semaphore\SemaphoreSender;
use Oka\Messenger\Transport\Semaphore\SemaphoreStamp;
use Oka\Messenger\Transport\Semaphore\Tests\Fixtures\DummyMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer as SerializerComponent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class SemaphoreIntegrationTest extends TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        if (false === \extension_loaded('sysvmsg')) {
            self::markTestSkipped('Semaphore extension (sysvmsg) is required.');

            return;
        }

        $dsn = getenv('MESSENGER_SEMAPHORE_DSN') ?: 'semaphore://' . __FILE__;
        $this->connection = Connection::fromDsn($dsn);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (null !== $this->connection) {
            $this->connection->close();
        }
    }

    public function testConnectionSendAndGet(): void
    {
        $this->connection->send('{"message": "Hi"}', ['type' => DummyMessage::class]);
        $message = $this->connection->get();

        self::assertEquals('{"message": "Hi"}', $message->getBody());
        self::assertEquals(['type' => DummyMessage::class], $message->getHeaders());
    }

    public function testItSendsAndReceivesMessages(): void
    {
        $serializer = $this->createSerializer();

        $sender = new SemaphoreSender($this->connection, $serializer);
        $receiver = new SemaphoreReceiver($this->connection, $serializer);

        $sender->send($first = new Envelope(new DummyMessage('First')));
        $sender->send($second = new Envelope(new DummyMessage('Second')));

        $envelopes = iterator_to_array($receiver->get());

        self::assertCount(1, $envelopes);

        /** @var Envelope $envelope */
        $envelope = $envelopes[0];

        self::assertEquals($first->getMessage(), $envelope->getMessage());
        self::assertInstanceOf(SemaphoreStamp::class, $envelope->last(SemaphoreStamp::class));

        $envelopes = iterator_to_array($receiver->get());

        self::assertCount(1, $envelopes);

        /** @var Envelope $envelope */
        $envelope = $envelopes[0];

        self::assertEquals($second->getMessage(), $envelope->getMessage());
        self::assertInstanceOf(SemaphoreStamp::class, $envelope->last(SemaphoreStamp::class));

        self::assertEmpty(iterator_to_array($receiver->get()));
    }

    public function testItCountMessages(): void
    {
        $serializer = $this->createSerializer();

        $this->connection->close();
        $this->connection->setup();

        $sender = new SemaphoreSender($this->connection, $serializer);

        $sender->send(new Envelope(new DummyMessage('First')));
        $sender->send(new Envelope(new DummyMessage('Second')));
        $sender->send(new Envelope(new DummyMessage('Third')));

        self::assertSame(3, $this->connection->getMessageCount());
    }

    private function createSerializer(): SerializerInterface
    {
        return new Serializer(
            new SerializerComponent\Serializer(
                [new ObjectNormalizer(), new ArrayDenormalizer()],
                ['json' => new JsonEncoder()]
            )
        );
    }
}
