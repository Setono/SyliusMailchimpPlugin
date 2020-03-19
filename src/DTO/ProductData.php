<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DTO;

/**
 * This class represents the product data in Mailchimp
 */
final class ProductData extends DataTransferObject
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
    public $description;

    /** @var string|null */
    public $type;

    /** @var string|null */
    public $vendor;

    /** @var string|null */
    public $image_url;

    /** @var \Setono\SyliusMailchimpPlugin\DTO\ProductVariantData[] */
    public $variants;

    /** @var array */
    public $images = [];

    /** @var string|null */
    public $published_at_foreign;
}
