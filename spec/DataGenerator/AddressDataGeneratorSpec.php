<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\DataGenerator;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\DataGenerator\AddressDataGenerator;
use Setono\SyliusMailchimpPlugin\DataGenerator\AddressDataGeneratorInterface;
use Sylius\Component\Core\Model\AddressInterface;

class AddressDataGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddressDataGenerator::class);
    }

    public function it_implements_address_data_generator_interface(): void
    {
        $this->shouldImplement(AddressDataGeneratorInterface::class);
    }

    public function it_generates_address_data(AddressInterface $address): void
    {
        $address->getStreet()->willReturn('Boulevard of Broken Dreams');
        $address->getCity()->willReturn('Los Angeles');
        $address->getPostcode()->willReturn('90210');
        $address->getProvinceName()->willReturn(null);
        $address->getProvinceCode()->willReturn(null);
        $address->getCountryCode()->willReturn('US');

        $addressData = $this->generate($address);
        $addressData->toArray()->shouldEqual([
            'address1' => 'Boulevard of Broken Dreams',
            'address2' => null,
            'city' => 'Los Angeles',
            'province' => null,
            'province_code' => null,
            'postal_code' => '90210',
            'country' => null,
            'country_code' => 'US',
        ]);
    }
}
