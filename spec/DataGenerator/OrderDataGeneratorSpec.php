<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\DataGenerator;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderDataGenerator;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DTO\OrderData;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

class OrderDataGeneratorSpec extends ObjectBehavior
{
    public function let(CurrencyConverterInterface $currencyConverter): void
    {
        $currencyConverter
            ->convert(Argument::type('integer'), Argument::type('string'), Argument::type('string'))
            ->willReturnArgument()
        ;

        $this->beConstructedWith($currencyConverter);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderDataGenerator::class);
    }

    public function it_implements_order_data_generator_interface(): void
    {
        $this->shouldImplement(OrderDataGeneratorInterface::class);
    }

    public function it_generates_order_data(
        OrderInterface $order,
        CustomerInterface $customer,
        AddressInterface $shippingAddress,
        ChannelInterface $channel,
        CurrencyInterface $baseCurrency,
        Collection $orders
    ): void {
        $order->getCustomer()->willReturn($customer);
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getChannel()->willReturn($channel);
        $order->getNumber()->willReturn('#00001');
        $order->getTotal()->willReturn(99500);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getTaxTotal()->willReturn(19900);
        $order->getShippingTotal()->willReturn(5000);

        $baseCurrency->getCode()->willReturn('USD');

        $channel->getBaseCurrency()->willReturn($baseCurrency);

        $orders->count()->willReturn(10);

        $customer->getId()->willReturn(1);
        $customer->getEmail()->willReturn('john.doe@example.com');
        $customer->getFirstName()->willReturn('John');
        $customer->getLastName()->willReturn('Doe');
        $customer->isSubscribedToNewsletter()->willReturn(true);
        $customer->getOrders()->willReturn($orders);

        $shippingAddress->getStreet()->willReturn('Boulevard of Broken Dreams');
        $shippingAddress->getCity()->willReturn('Los Angeles');
        $shippingAddress->getPostcode()->willReturn('90210');
        $shippingAddress->getProvinceName()->willReturn(null);
        $shippingAddress->getProvinceCode()->willReturn(null);
        $shippingAddress->getCountryCode()->willReturn('US');

        $this->generate($order)->shouldReturnAnInstanceOf(OrderData::class);
    }
}
