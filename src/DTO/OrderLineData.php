<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

/**
 * This class represents the order line data in Mailchimp
 *
 * @todo typehint nullable properties
 */
final class OrderLineData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $product_id;

    /** @var string */
    public $product_variant_id;

    /** @var int */
    public $quantity;

    /** @var float */
    public $price;
}
