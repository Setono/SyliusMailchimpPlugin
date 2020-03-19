<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\DataGenerator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusMailchimpPlugin\DataGenerator\CustomerDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderDataGenerator;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderLineDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DTO\CustomerData;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

class OrderDataGeneratorSpec extends ObjectBehavior
{
    public function let(
        CurrencyConverterInterface $currencyConverter,
        CustomerDataGeneratorInterface $customerDataGenerator,
        OrderLineDataGeneratorInterface $orderLineDataGenerator
    ): void {
        $currencyConverter
            ->convert(Argument::type('integer'), Argument::type('string'), Argument::type('string'))
            ->willReturnArgument();

        $customerDataGenerator
            ->generate(Argument::any(), Argument::any())
            ->willReturn(new CustomerData([
                'id' => 'id',
                'email_address' => 'john.doe@example.com',
                'opt_in_status' => true,
            ]));

        $this->beConstructedWith($currencyConverter, $customerDataGenerator, $orderLineDataGenerator);
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
        $order->getTotal()->willReturn(99595);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getTaxTotal()->willReturn(19950);
        $order->getShippingTotal()->willReturn(5050);
        $order->getItems()->willReturn(new ArrayCollection());

        $baseCurrency->getCode()->willReturn('USD');

        $channel->getBaseCurrency()->willReturn($baseCurrency);

        $orders->count()->willReturn(10);

        $data = $this->generate($order);
        $data->toArray()->shouldEqual([
            'id' => '#00001',
            'currency_code' => 'USD',
            'order_total' => 995.95,
            'tax_total' => 199.5,
            'shipping_total' => 50.5,
            'customer' => [
                'id' => 'id',
                'email_address' => 'john.doe@example.com',
                'opt_in_status' => true,
                'company' => null,
                'first_name' => null,
                'last_name' => null,
                'orders_count' => null,
                'total_spent' => null,
                'address' => null,
            ],
            'lines' => [],
        ]);
    }
}
