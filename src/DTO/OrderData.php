<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

/**
 * This class represents the order data in Mailchimp
 *
 * @todo typehint nullable properties
 */
final class OrderData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $currency_code;

    /** @var float */
    public $order_total;

    /** @var float */
    public $tax_total;

    /** @var float */
    public $shipping_total;

    /**
     * For nested type casting to work your Docblock definition needs to be a Fully Qualified Class Name
     * See https://github.com/spatie/data-transfer-object#automatic-casting-of-nested-array-dtos
     *
     * @var \Setono\SyliusMailchimpPlugin\DTO\CustomerData
     */
    public $customer;

    /** @var \Setono\SyliusMailchimpPlugin\DTO\OrderLineData[] */
    public $lines;
}
