<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusMailchimpPlugin\DTO\CustomerData;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerDataGenerator extends DataGenerator implements CustomerDataGeneratorInterface
{
    /** @var AddressDataGeneratorInterface */
    private $addressDataGenerator;

    public function __construct(EventDispatcherInterface $eventDispatcher, AddressDataGeneratorInterface $addressDataGenerator)
    {
        parent::__construct($eventDispatcher);

        $this->addressDataGenerator = $addressDataGenerator;
    }

    public function generate(CustomerInterface $customer, AddressInterface $address = null): CustomerData
    {
        $addressData = null;
        if (null !== $address) {
            $addressData = $this->addressDataGenerator->generate($address);
        }

        return new CustomerData([
            'id' => (string) $customer->getId(),
            'email_address' => $customer->getEmail(),
            'opt_in_status' => $customer->isSubscribedToNewsletter(),
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
            'orders_count' => $customer->getOrders()->count(),
            'address' => $addressData,
        ]);
    }
}
