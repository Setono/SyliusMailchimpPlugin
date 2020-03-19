<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Psr\EventDispatcher\EventDispatcherInterface;
use Safe\Exceptions\StringsException;
use Setono\SyliusMailchimpPlugin\DTO\OrderData;
use Setono\SyliusMailchimpPlugin\Event\OrderDataGeneratedEvent;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Webmozart\Assert\Assert;

/**
 * See documentation here: https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
 */
final class OrderDataGenerator extends CurrencyConverterAwareDataGenerator implements OrderDataGeneratorInterface
{
    /** @var CustomerDataGeneratorInterface */
    private $customerDataGenerator;

    /** @var OrderLineDataGeneratorInterface */
    private $orderLineDataGenerator;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CurrencyConverterInterface $currencyConverter,
        CustomerDataGeneratorInterface $customerDataGenerator,
        OrderLineDataGeneratorInterface $orderLineDataGenerator
    ) {
        $this->customerDataGenerator = $customerDataGenerator;
        $this->orderLineDataGenerator = $orderLineDataGenerator;

        parent::__construct($eventDispatcher, $currencyConverter);
    }

    /**
     * @throws StringsException
     */
    public function generate(OrderInterface $order): OrderData
    {
        /** @var CustomerInterface|null $customer */
        $customer = $order->getCustomer();
        Assert::notNull($customer);

        $shippingAddress = $order->getShippingAddress();
        Assert::notNull($shippingAddress);

        $channel = $order->getChannel();
        Assert::notNull($channel);

        $baseCurrencyCode = self::getBaseCurrencyCode($channel);

        $data = [
            'id' => $order->getNumber(),
            //'campaign_id' => '', // todo
            //'landing_site' => '', // todo
            'currency_code' => $baseCurrencyCode,
            'order_total' => $this->convertPrice($order->getTotal(), $order->getCurrencyCode(), $baseCurrencyCode),
            //'discount_total' => '', // todo
            'tax_total' => $this->convertPrice($order->getTaxTotal(), $order->getCurrencyCode(), $baseCurrencyCode),
            'shipping_total' => $this->convertPrice($order->getShippingTotal(), $order->getCurrencyCode(), $baseCurrencyCode),
            'customer' => $this->customerDataGenerator->generate($customer, $shippingAddress),
            'lines' => [],
        ];

        foreach ($order->getItems() as $orderItem) {
            $data['lines'][] = $this->orderLineDataGenerator->generate($orderItem);
        }

        $orderData = new OrderData($data);

        $this->eventDispatcher->dispatch(new OrderDataGeneratedEvent($orderData, [
            'order' => $order,
            'customer' => $customer,
            'shippingAddress' => $shippingAddress,
            'channel' => $channel,
            'baseCurrencyCode' => $baseCurrencyCode,
        ]));

        return $orderData;
    }
}
