<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Resource\Model\ResourceInterface;

interface HandlerInterface
{
    public function handle(ResourceInterface $resource): void;
}
