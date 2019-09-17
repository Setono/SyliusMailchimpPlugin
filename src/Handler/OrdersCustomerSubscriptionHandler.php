<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class OrdersCustomerSubscriptionHandler implements OrdersCustomerSubscriptionHandlerInterface
{
    /** @var CustomerSubscriptionHandlerInterface */
    private $customerSubscriptionHandler;

    public function __construct(CustomerSubscriptionHandlerInterface $customerSubscriptionHandler)
    {
        $this->customerSubscriptionHandler = $customerSubscriptionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResourceInterface $resource): void
    {
        Assert::isInstanceOf($resource, OrderInterface::class);

        /** @var OrderInterface $order */
        $order = $resource;

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $this->customerSubscriptionHandler->handle(
            $customer,
            $channel->getCode(),
            $order->getLocaleCode()
        );
    }
}
