<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use DrewM\MailChimp\MailChimp;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class MailchimpApiClient implements MailchimpApiClientInterface
{
    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    public function __construct(MailchimpConfigContextInterface $mailchimpConfigContext)
    {
        $this->mailchimpConfigContext = $mailchimpConfigContext;
    }

    /**
     * @param string $email
     * @param string $listId
     *
     * @throws MailchimpApiException
     */
    public function exportEmail(string $email, string $listId): void
    {
        try {
            $this->request()->post(sprintf('lists/%s/members', $listId), [
                'email_address' => $email,
                'status' => 'subscribed',
            ]);
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * @param string $email
     * @param string $listId
     *
     * @throws MailchimpApiException
     */
    public function removeEmail(string $email, string $listId): void
    {
        $request = $this->request();

        try {
            $request->delete(sprintf('lists/%s/members/%s',
                    $listId,
                    $request->subscriberHash($email)
                )
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function exportOrder(OrderInterface $order): void
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        $exportData = $this->getExportData($order, $customer);

        $mailLists = $this->mailchimpConfigContext->getConfig()->getLists();

        foreach ($mailLists as $mailList) {
            $storeData = [
                'id' => $this->mailchimpConfigContext->getConfig()->getStoreId(),
                'list_id' => $mailList->getListId(),
                'name' => 'Store_' . $this->mailchimpConfigContext->getConfig()->getStoreId(),
                'domain' => 'Domain' . $this->mailchimpConfigContext->getConfig()->getStoreId(),
                'currency_code' => (string) $order->getCurrencyCode() ?: 'USD',
            ];

            try {
                $this->request()->post('/ecommerce/stores',
                    $storeData
                );
            } catch (\Exception $exception) {
                throw new MailchimpApiException($exception->getMessage());
            }
        }

        $this->exportOrderProducts($order);

        try {
            $this->request()->post(sprintf('/ecommerce/stores/%s/orders',
                $this->mailchimpConfigContext->getConfig()->getStoreId()),
                $exportData
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function removeOrder(OrderInterface $order): void
    {
        try {
            $this->request()->delete(sprintf('/ecommerce/stores/%s/orders/%s',
                    $this->mailchimpConfigContext->getConfig()->getStoreId(),
                    $order->getId()
                )
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * @return MailChimp
     *
     * @throws MailchimpApiException
     */
    private function request(): MailChimp
    {
        try {
            $config = $this->mailchimpConfigContext->getConfig();
            $mailchimpClient = new MailChimp($config->getApiKey());
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }

        return $mailchimpClient;
    }

    private function getExportData(OrderInterface $order, CustomerInterface $customer): array
    {
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
            'currency_code' => (string) $order->getCurrencyCode() ?: 'USD',
            'order_total' => $order->getTotal() / 100,
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
                'price' => $orderItem->getTotal() / 100,
            ];
        }

        return $exportData;
    }

    private function exportOrderProducts(OrderInterface $order): void
    {
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $variant = $orderItem->getVariant();

            if (null === $product || null === $variant) {
                continue;
            }

            $productData = [
                'id' => (string) $product->getId(),
                'title' => (string) $product->getName(),
                'variants' => [],
            ];

            $productData['variants'][] = [
                'id' => (string) $variant->getId(),
                'title' => (string) $variant->getName(),
            ];

            try {
                $this->request()->post(sprintf('/ecommerce/stores/%s/products',
                    $this->mailchimpConfigContext->getConfig()->getStoreId()),
                    $productData
                );
            } catch (\Exception $exception) {
                throw new MailchimpApiException($exception->getMessage());
            }
        }
    }
}
