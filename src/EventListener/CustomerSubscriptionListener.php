<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Setono\SyliusMailchimpPlugin\Handler\CustomerSubscriptionHandlerInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Webmozart\Assert\Assert;

final class CustomerSubscriptionListener
{
    /** @var CustomerSubscriptionHandlerInterface */
    private $customerSubscriptionHandler;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(
        CustomerSubscriptionHandlerInterface $customerSubscriptionHandler,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext
    ) {
        $this->customerSubscriptionHandler = $customerSubscriptionHandler;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function postCreate(ResourceControllerEvent $event): void
    {
        /** @var CustomerInterface $customer */
        $customer = $event->getSubject();

        Assert::isInstanceOf($customer, CustomerInterface::class);

        $this->customerSubscriptionHandler->handle(
            $customer,
            $this->channelContext->getChannel()->getCode(),
            $this->localeContext->getLocaleCode()
        );
    }
}
