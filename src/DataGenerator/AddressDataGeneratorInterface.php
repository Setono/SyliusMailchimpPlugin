<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Setono\SyliusMailchimpPlugin\DTO\AddressData;
use Sylius\Component\Addressing\Model\AddressInterface;

interface AddressDataGeneratorInterface extends DataGeneratorInterface
{
    public function generate(AddressInterface $address): AddressData;
}
