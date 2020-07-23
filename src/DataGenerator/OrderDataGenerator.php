<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Safe\Exceptions\StringsException;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Webmozart\Assert\Assert;

/**
 * See documentation here: https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
 */
final class OrderDataGenerator extends DataGenerator implements OrderDataGeneratorInterface
{
    /** @var CurrencyConverterInterface */
    private $currencyConverter;

    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    public function generate(OrderInterface $order): array
    {
        /** @var CustomerInterface|null $customer */
        $customer = $order->getCustomer();
        Assert::notNull($customer);

        $shippingAddress = $order->getShippingAddress();
        Assert::notNull($shippingAddress);

        $channel = $order->getChannel();
        Assert::notNull($channel);

        $baseCurrencyCode = self::getBaseCurrencyCode($channel);
        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode);

        $data = [
            'id' => $order->getNumber(),
            //'campaign_id' => '', // todo
            //'landing_site' => '', // todo
            'currency_code' => $baseCurrencyCode,
            'order_total' => $this->convertPrice(
                $order->getTotal(),
                $currencyCode,
                $baseCurrencyCode
            ),
            //'discount_total' => '', // todo
            'tax_total' => $this->convertPrice($order->getTaxTotal(), $currencyCode, $baseCurrencyCode),
            'shipping_total' => $this->convertPrice($order->getShippingTotal(), $currencyCode, $baseCurrencyCode),
            'customer' => [
                'id' => (string) $customer->getId(),
                'email_address' => $customer->getEmail(),
                'opt_in_status' => $customer->isSubscribedToNewsletter(),
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'orders_count' => $customer->getOrders()->count(),
                'address' => [
                    'address1' => $shippingAddress->getStreet(),
                    'city' => $shippingAddress->getCity(),
                    'province' => $shippingAddress->getProvinceName(),
                    'province_code' => $shippingAddress->getProvinceCode(),
                    'postal_code' => $shippingAddress->getPostcode(),
                    'country_code' => $shippingAddress->getCountryCode(),
                ],
            ],
            'lines' => [],
        ];

        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $variant = $orderItem->getVariant();

            if (null === $product || null === $variant) {
                continue;
            }

            $data['lines'][] = [
                'id' => (string) $orderItem->getId(),
                'product_id' => $product->getCode(),
                'product_variant_id' => $variant->getCode(),
                'quantity' => $orderItem->getQuantity(),
                'price' => $this->convertPrice($orderItem->getTotal(), $currencyCode, $baseCurrencyCode),
            ];
        }

        return self::filterArrayRecursively($data);
    }

    private function convertPrice(int $amount, string $sourceCurrencyCode, string $targetCurrencyCode): float
    {
        return round($this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode) / 100, 2);
    }
}
