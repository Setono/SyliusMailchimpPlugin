<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomer;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class CustomerRegisterSubscriber implements EventSubscriberInterface
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.customer.post_register' => 'subscribeCustomerToNewsletter',
        ];
    }

    public function subscribeCustomerToNewsletter(ResourceControllerEvent $event): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $event->getSubject();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        if ($customer->isSubscribedToNewsletter()) {
            $this->commandBus->dispatch(new PushCustomer($customer->getId()));
        }
    }
}
