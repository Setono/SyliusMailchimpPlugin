<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

/**
 * This class represents the store data in Mailchimp
 */
final class StoreData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $list_id;

    /** @var string */
    public $name;

    /** @var string|null */
    public $platform;

    /** @var string|null */
    public $domain;

    /** @var bool|null */
    public $is_syncing;

    /** @var string|null */
    public $email_address;

    /** @var string */
    public $currency_code;

    /** @var string|null */
    public $money_format;

    /** @var string|null */
    public $primary_locale;

    /** @var string|null */
    public $timezone;

    /** @var string|null */
    public $phone;

    /** @var \Setono\SyliusMailchimpPlugin\DTO\AddressData|null */
    public $address;
}
