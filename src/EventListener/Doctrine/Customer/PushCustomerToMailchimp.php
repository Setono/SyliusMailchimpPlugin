<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener\Doctrine\Customer;

use function array_key_exists;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomer;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PushCustomerToMailchimp
{
    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(MessageBusInterface $setonoSyliusMailChimpMessageBus)
    {
        $this->messageBus = $setonoSyliusMailChimpMessageBus;
    }

    public function postPersist(CustomerInterface $customer, LifecycleEventArgs $args): void
    {
        if (!$customer->isSubscribedToNewsletter()) {
            return;
        }

        $message = new PushCustomer($customer->getId());
        $this->messageBus->dispatch($message);
    }

    public function postUpdate(CustomerInterface $customer, LifecycleEventArgs $args): void
    {
        $changesSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($customer);

        if (!array_key_exists('subscribedToNewsletter', $changesSet) || false === $changesSet['subscribedToNewsletter'][1]) {
            return;
        }

        $message = new PushCustomer($customer->getId());
        $this->messageBus->dispatch($message);
    }
}
