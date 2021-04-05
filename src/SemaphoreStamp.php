<?php

namespace Oka\Messenger\Transport\Semaphore;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class SemaphoreStamp implements NonSendableStampInterface
{
    private $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
