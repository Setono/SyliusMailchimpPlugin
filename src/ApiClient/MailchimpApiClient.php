<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use DrewM\MailChimp\MailChimp as Client;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;

final class MailchimpApiClient implements MailchimpApiClientInterface
{
    /** @var Client */
    private $apiClient;

    public function __construct(Client $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * {@inheritdoc}
     */
    public function isApiKeyValid(): bool
    {
        try {
            $this->apiClient->get('/lists');
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAudienceIdExists(string $audienceId): bool
    {
        try {
            $list = $this->apiClient->get(sprintf('/lists/%s', $audienceId));

            return isset($list['id']);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isStoreIdExists(string $storeId): bool
    {
        try {
            $store = $this->apiClient->get(sprintf('/ecommerce/stores/%s', $storeId));

            return isset($store['id']);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMergeFields(string $audienceId, array $requiredMergeFields): array
    {
        try {
            return $this->apiClient->get(
                sprintf('/lists/%s/merge-fields', $audienceId)
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createStore(array $storeData): void
    {
        try {
            $this->apiClient->post(
                '/ecommerce/stores',
                $storeData
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exportEmail(string $listId, string $email, array $options = []): bool
    {
        try {
            $response = $this->apiClient->post(sprintf('/lists/%s/members', $listId), $options + [
                'email_address' => $email,
                'status' => 'subscribed',
            ]);

            return true;
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeEmail(string $listId, string $email): void
    {
        try {
            $this->apiClient->delete(sprintf(
                '/lists/%s/members/%s',
                $listId,
                $this->apiClient->subscriberHash($email)
            ));
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exportProduct(string $storeId, array $productData): void
    {
        try {
            $this->apiClient->post(
                sprintf('/ecommerce/stores/%s/products', $storeId),
                $productData
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exportOrder(string $storeId, array $orderData): void
    {
        try {
            $this->apiClient->post(
                sprintf('/ecommerce/stores/%s/orders', $storeId),
                $orderData
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrder(string $storeId, string $orderId): void
    {
        try {
            $this->apiClient->delete(sprintf(
                '/ecommerce/stores/%s/orders/%s',
                $storeId,
                $orderId
            ));
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }
}
