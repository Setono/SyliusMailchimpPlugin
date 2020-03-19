<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\DataGenerator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderLineDataGenerator;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderLineDataGeneratorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

class OrderLineDataGeneratorSpec extends ObjectBehavior
{
    public function let(CurrencyConverterInterface $currencyConverter): void
    {
        $currencyConverter
            ->convert(Argument::type('integer'), Argument::type('string'), Argument::type('string'))
            ->willReturnArgument();

        $this->beConstructedWith($currencyConverter);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderLineDataGenerator::class);
    }

    public function it_implements_order_line_data_generator_interface(): void
    {
        $this->shouldImplement(OrderLineDataGeneratorInterface::class);
    }

    public function it_generates_order_line_data(
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        OrderInterface $order,
        ChannelInterface $channel,
        CurrencyInterface $baseCurrency
    ): void {
        $orderItem->getProduct()->willReturn($product);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getId()->willReturn(1);
        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getTotal()->willReturn(125);

        $product->getCode()->willReturn('product1');

        $productVariant->getCode()->willReturn('product-variant1');

        $order->getChannel()->willReturn($channel);
        $order->getCurrencyCode()->willReturn('USD');

        $channel->getBaseCurrency()->willReturn($baseCurrency);

        $baseCurrency->getCode()->willReturn('USD');

        $data = $this->generate($orderItem);
        $data->toArray()->shouldEqual([
            'id' => '1',
            'product_id' => 'product1',
            'product_variant_id' => 'product-variant1',
            'quantity' => 2,
            'price' => 1.25,
        ]);
    }
}
