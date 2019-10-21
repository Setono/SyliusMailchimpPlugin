<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Setono\SyliusMailchimpPlugin\DTO\AddressData;
use Sylius\Component\Addressing\Model\AddressInterface;

final class AddressDataGenerator extends DataGenerator implements AddressDataGeneratorInterface
{
    public function generate(AddressInterface $address): AddressData
    {
        return AddressData::createFromAddress($address);
    }
}
