<?php
namespace Oka\Messenger\Transport\Semaphore;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * Symfony Messenger sender to send messages to Semaphore.
 *
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class SemaphoreSender implements SenderInterface
{
    private $connection;
    private $serializer;

    public function __construct(Connection $connection, SerializerInterface $serializer = null)
    {
        $this->connection = $connection;
        $this->serializer = $serializer ?? new PhpSerializer();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Messenger\Transport\Sender\SenderInterface::send()
     */
    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);

        /** @var \Symfony\Component\Messenger\Stamp\DelayStamp|null $delayStamp */
        $delayStamp = $envelope->last(DelayStamp::class);
        $delay = null !== $delayStamp ? $delayStamp->getDelay() : 0;

        $this->connection->send(
            $encodedMessage['body'],
            $encodedMessage['headers'] ?? [],
            $delay,
            $envelope->last(SemaphoreStamp::class)
        );

        return $envelope;
    }
}
