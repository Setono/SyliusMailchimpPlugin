<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Safe\Exceptions\StringsException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductDataGenerator extends DataGenerator implements ProductDataGeneratorInterface
{
    /** @var ProductVariantDataGeneratorInterface */
    private $productVariantDataGenerator;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        ProductVariantDataGeneratorInterface $productVariantDataGenerator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->productVariantDataGenerator = $productVariantDataGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(
        ProductInterface $product,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant = null
    ): array {
        $url = self::generateUrl($this->urlGenerator, $channel, 'sylius_shop_product_show', [
            'slug' => $product->getSlug(),
        ]);

        $data = [
            'id' => $product->getCode(),
            'title' => $product->getName(),
            'url' => $url,
            'description' => $product->getDescription(),
        ];

        $variants = null === $productVariant ? $product->getVariants() : [$productVariant];

        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $data['variants'][] = $this->productVariantDataGenerator->generate($variant, $channel);
        }

        return self::filterArrayRecursively($data);
    }
}
