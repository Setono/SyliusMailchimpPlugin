<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Setono\SyliusMailchimpPlugin\DTO\ProductVariantData;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantDataGeneratorInterface extends DataGeneratorInterface
{
    public function generate(ProductVariantInterface $productVariant, ChannelInterface $channel): ProductVariantData;
}
