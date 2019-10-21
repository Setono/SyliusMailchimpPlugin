<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

use Spatie\DataTransferObject\DataTransferObject;
use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * This class represents the address data in Mailchimp
 *
 * See https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-customers/
 */
final class AddressData extends DataTransferObject
{
    /** @var string|null */
    public $address1;

    /** @var string|null */
    public $address2;

    /** @var string|null */
    public $city;

    /** @var string|null */
    public $province;

    /** @var string|null */
    public $province_code;

    /** @var string|null */
    public $postal_code;

    /** @var string|null */
    public $country;

    /** @var string|null */
    public $country_code;

    public static function createFromAddress(AddressInterface $address): AddressData
    {
        return new self([
            'address1' => $address->getStreet(),
            'city' => $address->getCity(),
            'province' => $address->getProvinceName(),
            'province_code' => $address->getProvinceCode(),
            'postal_code' => $address->getPostcode(),
            'country_code' => $address->getCountryCode(),
        ]);
    }
}
