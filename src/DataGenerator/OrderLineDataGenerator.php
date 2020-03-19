<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Safe\Exceptions\StringsException;
use Setono\SyliusMailchimpPlugin\DTO\OrderLineData;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

final class OrderLineDataGenerator extends CurrencyConverterAwareDataGenerator implements OrderLineDataGeneratorInterface
{
    /**
     * @throws StringsException
     */
    public function generate(OrderItemInterface $orderItem): OrderLineData
    {
        $product = $orderItem->getProduct();
        Assert::notNull($product);

        $variant = $orderItem->getVariant();
        Assert::notNull($variant);

        /** @var OrderInterface|null $order */
        $order = $orderItem->getOrder();
        Assert::notNull($order);

        $channel = $order->getChannel();
        Assert::notNull($channel);

        $baseCurrencyCode = self::getBaseCurrencyCode($channel);

        return new OrderLineData([
            'id' => (string) $orderItem->getId(),
            'product_id' => $product->getCode(),
            'product_variant_id' => $variant->getCode(),
            'quantity' => $orderItem->getQuantity(),
            'price' => $this->convertPrice($orderItem->getTotal(), $order->getCurrencyCode(), $baseCurrencyCode),
        ]);
    }
}
