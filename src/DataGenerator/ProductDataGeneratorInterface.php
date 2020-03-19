<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Setono\SyliusMailchimpPlugin\DTO\ProductData;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductDataGeneratorInterface extends DataGeneratorInterface
{
    /**
     * @param ProductVariantInterface|null $productVariant If a product variant is given it will only generate data for this
     */
    public function generate(
        ProductInterface $product,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant = null
    ): ProductData;
}
