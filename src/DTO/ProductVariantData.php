<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

/**
 * This class represents the product variant data in Mailchimp
 */
final class ProductVariantData extends DataTransferObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $title;

    /** @var string|null */
    public $handle;

    /** @var string|null */
    public $url;

    /** @var string|null */
    public $sku;

    /** @var float|null */
    public $price;

    /** @var int|null */
    public $inventory_quantity;

    /** @var string|null */
    public $image_url;

    /** @var string|null */
    public $backorders;

    /** @var string|null */
    public $visibility;
}
