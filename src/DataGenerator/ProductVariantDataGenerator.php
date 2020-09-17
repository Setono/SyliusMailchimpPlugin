<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ProductVariantDataGenerator extends DataGenerator implements ProductVariantDataGeneratorInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(ProductVariantInterface $productVariant, ChannelInterface $channel): array
    {
        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $url = self::generateUrl($this->urlGenerator, $channel, 'sylius_shop_product_show', [
            'slug' => $product->getSlug(),
        ]);

        $data = [
            'id' => $productVariant->getCode(),
            'title' => $productVariant->getName() ?? $product->getName(), 
            'url' => $url,
            'inventory_quantity' => $productVariant->isTracked() ? $productVariant->getOnHand() : null,
            'backorders' => $productVariant->isTracked() ? (string) $productVariant->getOnHold() : null,
            // 'price' => '', // todo
            // 'image_url' => '', // todo
        ];

        return self::filterArrayRecursively($data);
    }
}
