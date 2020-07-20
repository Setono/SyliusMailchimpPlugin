<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EntityListener\Customer;

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
        if ($customer->isSubscribedToNewsletter()) {
            if (null !== $customer->getFirstName() && null !== $customer->getLastName()) {
                $message = new PushCustomer($customer->getId(), false);
            } else {
                $message = new PushCustomer($customer->getId(), true);
            }
            $this->messageBus->dispatch($message);
        }
    }

    public function postUpdate(CustomerInterface $customer, LifecycleEventArgs $args): void
    {
        $changesSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($customer);

        if (array_key_exists('subscribedToNewsletter', $changesSet) && true === $changesSet['subscribedToNewsletter'][1]) {
            if (null !== $customer->getFirstName() && null !== $customer->getLastName()) {
                $message = new PushCustomer($customer->getId(), false);
            } else {
                $message = new PushCustomer($customer->getId(), true);
            }
            $this->messageBus->dispatch($message);
        }
    }
}
