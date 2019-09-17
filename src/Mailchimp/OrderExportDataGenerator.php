<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrderExportDataGenerator implements OrderExportDataGeneratorInterface
{
    /** @var RouterInterface */
    private $router;

    /** @var CurrencyConverterInterface */
    private $currencyConverter;

    public function __construct(
        RouterInterface $router,
        CurrencyConverterInterface $currencyConverter
    ) {
        $this->router = $router;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function generateStoreExportData(MailchimpListInterface $mailchimpList): array
    {
        return [
            'id' => $mailchimpList->getStoreId(),
            'list_id' => $mailchimpList->getListId(),
            'name' => $mailchimpList->getStoreId(),
            'domain' => $mailchimpList->getStoreId(),
            'currency_code' => $mailchimpList->getStoreCurrencyCode(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generateOrderExportData(OrderInterface $order, MailchimpListInterface $mailchimpList): array
    {
        $customer = $order->getCustomer();
        Assert::notNull($customer);

        $shippingAddress = $order->getShippingAddress();
        Assert::notNull($shippingAddress);

        $exportData = [
            'id' => (string) $order->getId(),
            'customer' => [
                'id' => (string) $customer->getId(),
                'email_address' => (string) $customer->getEmail(),
                'opt_in_status' => $customer->isSubscribedToNewsletter(),
                'company' => (string) $shippingAddress->getCompany(),
                'first_name' => (string) $shippingAddress->getFirstName(),
                'last_name' => (string) $shippingAddress->getLastName(),
                'address' => [
                    'address1' => (string) $shippingAddress->getStreet(),
                    'city' => (string) $shippingAddress->getCity(),
                    'province' => (string) $shippingAddress->getProvinceName(),
                    'province_code' => (string) $shippingAddress->getProvinceCode(),
                    'postal_code' => (string) $shippingAddress->getPostcode(),
                    'country_code' => (string) $shippingAddress->getCountryCode(),
                ],
            ],
            'currency_code' => (string) $mailchimpList->getStoreCurrencyCode(),
            'order_total' => $this->convertPrice(
                $order->getTotal(),
                $order->getCurrencyCode(),
                $mailchimpList->getStoreCurrencyCode()
            ),
            'lines' => [],
        ];

        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $variant = $orderItem->getVariant();

            if (null === $product || null === $variant) {
                continue;
            }

            $exportData['lines'][] = [
                'id' => (string) $orderItem->getId(),
                'product_id' => (string) $product->getId(),
                'product_variant_id' => (string) $variant->getId(),
                'quantity' => $orderItem->getQuantity(),
                'price' => $this->convertPrice(
                    $orderItem->getTotal(),
                    $order->getCurrencyCode(),
                    $mailchimpList->getStoreCurrencyCode()
                ),
            ];
        }

        return $exportData;
    }

    /**
     * {@inheritdoc}
     */
    public function generateOrderProductsExportData(OrderInterface $order, MailchimpListInterface $mailchimpList): array
    {
        $productsData = [];

        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $variant = $orderItem->getVariant();

            if (null === $product || null === $variant) {
                continue;
            }

            $url = $this->router->generate('sylius_shop_product_show', [
                'slug' => $product->getSlug(),
                '_locale' => $product->getTranslation()->getLocale(),
            ]);

            $channelPricing = $variant->getChannelPricingForChannel($order->getChannel());

            $productsData[] = [
                'id' => (string) $product->getId(),
                'title' => (string) $product->getName(),
                'url' => $url,
                'variants' => [
                    [
                        'id' => (string) $variant->getId(),
                        'title' => (string) $variant->getName(),
                        'url' => $url,
                        'sku' => (string) $variant->getCode(),
                        'price' => $channelPricing ? $this->convertPrice(
                            $channelPricing->getPrice(),
                            $order->getCurrencyCode(),
                            $mailchimpList->getStoreCurrencyCode()
                        ) : null,
                        'inventory_quantity' => $variant->isTracked() ? $variant->getOnHand() : null,
                        'backorders' => $variant->isTracked() ? $variant->getOnHold() : null,
                    ],
                ],
            ];
        }

        return $productsData;
    }

    private function convertPrice(int $amount, string $sourceCurrencyCode, string $targetCurrencyCode): string
    {
        return (string) ($this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode) / 100);
    }
}
