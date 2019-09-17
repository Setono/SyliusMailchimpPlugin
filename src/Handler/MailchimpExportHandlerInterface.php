<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpExportHandlerInterface
{
    public function handle(ResourceInterface $resource, int $limit): void;
}
