<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Setono\SyliusMailchimpPlugin\DTO\CustomerData;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerDataGeneratorInterface extends DataGeneratorInterface
{
    public function generate(CustomerInterface $customer, AddressInterface $address = null): CustomerData;
}
