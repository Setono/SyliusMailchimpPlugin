<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Psr\EventDispatcher\EventDispatcherInterface;
use Safe\Exceptions\StringsException;
use Setono\SyliusMailchimpPlugin\DTO\ProductData;
use Setono\SyliusMailchimpPlugin\Event\ProductDataGeneratedEvent;
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
        EventDispatcherInterface $eventDispatcher,
        ProductVariantDataGeneratorInterface $productVariantDataGenerator,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($eventDispatcher);

        $this->productVariantDataGenerator = $productVariantDataGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws StringsException
     */
    public function generate(
        ProductInterface $product,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant = null
    ): ProductData {
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

        $productData = new ProductData($data);

        $this->eventDispatcher->dispatch(new ProductDataGeneratedEvent($productData, [
            'product' => $product,
            'channel' => $channel,
            'productVariant' => $productVariant,
        ]));

        return $productData;
    }
}
