<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Psr\EventDispatcher\EventDispatcherInterface;
use Safe\Exceptions\StringsException;
use Setono\SyliusMailchimpPlugin\DTO\ProductVariantData;
use Setono\SyliusMailchimpPlugin\Event\ProductVariantDataGeneratedEvent;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ProductVariantDataGenerator extends DataGenerator implements ProductVariantDataGeneratorInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(EventDispatcherInterface $eventDispatcher, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($eventDispatcher);

        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws StringsException
     */
    public function generate(ProductVariantInterface $productVariant, ChannelInterface $channel): ProductVariantData
    {
        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $productVariantData = new ProductVariantData([
            'id' => $productVariant->getCode(),
            'title' => $productVariant->getName(),
            'url' => self::generateUrl($this->urlGenerator, $channel, 'sylius_shop_product_show', [
                'slug' => $product->getSlug(),
            ]),
            'inventory_quantity' => $productVariant->isTracked() ? $productVariant->getOnHand() : null,
            'backorders' => $productVariant->isTracked() ? $productVariant->getOnHold() : null,
            // 'price' => '', // todo
            // 'image_url' => '', // todo
        ]);

        $this->eventDispatcher->dispatch(new ProductVariantDataGeneratedEvent($productVariantData, [
            'product' => $product,
            'productVariant' => $productVariant,
        ]));

        return $productVariantData;
    }
}
