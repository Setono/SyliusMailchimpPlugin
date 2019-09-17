<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Setono\SyliusMailchimpPlugin\Mailchimp\OrderExportManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class OrderExportHandler implements OrderExportHandlerInterface
{
    /** @var OrderExportManagerInterface */
    private $orderExportManager;

    public function __construct(
        OrderExportManagerInterface $orderExportManager
    ) {
        $this->orderExportManager = $orderExportManager;
    }

    public function handle(ResourceInterface $resource): void
    {
        Assert::isInstanceOf($resource, OrderInterface::class);

        /** @var OrderInterface $order */
        $order = $resource;

        $this->orderExportManager->exportOrder($order);
    }
}
