<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

/**
 * This class represents the customer data in Mailchimp
 */
final class CustomerData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $email_address;

    /** @var bool */
    public $opt_in_status;

    /** @var string|null */
    public $company;

    /** @var string|null */
    public $first_name;

    /** @var string|null */
    public $last_name;

    /** @var int|null */
    public $orders_count;

    /** @var float|null */
    public $total_spent;

    /**
     * For nested type casting to work your Docblock definition needs to be a Fully Qualified Class Name
     * See https://github.com/spatie/data-transfer-object#automatic-casting-of-nested-array-dtos
     *
     * @var \Setono\SyliusMailchimpPlugin\DTO\AddressData|null
     */
    public $address;
}
