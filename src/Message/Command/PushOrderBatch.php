<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Command;

use Setono\DoctrineORMBatcher\Batch\BatchInterface;

final class PushOrderBatch implements CommandInterface
{
    /** @var BatchInterface */
    private $batch;

    public function __construct(BatchInterface $batch)
    {
        $this->batch = $batch;
    }

    public function getBatch(): BatchInterface
    {
        return $this->batch;
    }
}
