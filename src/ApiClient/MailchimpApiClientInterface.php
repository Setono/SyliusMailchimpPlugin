<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;

interface MailchimpApiClientInterface
{
    /**
     * @return bool
     */
    public function isApiKeyValid(): bool;

    /**
     * @param string $audienceId
     *
     * @return bool
     */
    public function isAudienceIdExists(string $audienceId): bool;

    /**
     * @param string $storeId
     *
     * @return bool
     */
    public function isStoreIdExists(string $storeId): bool;

    /**
     * @param string $audienceId
     * @param array $requiredMergeTags
     *
     * @return array
     *
     * @throws MailchimpApiException
     */
    public function getMergeFields(string $audienceId, array $requiredMergeTags): array;

    /**
     * @param array $storeData
     *
     * @throws MailchimpApiException
     */
    public function createStore(array $storeData): void;

    /**
     * @param string $listId
     * @param string $email
     * @param array $options
     *
     * @return bool
     *
     * @throws MailchimpApiException
     */
    public function exportEmail(string $listId, string $email, array $options): bool;

    /**
     * @param string $listId
     * @param string $email
     *
     * @throws MailchimpApiException
     */
    public function removeEmail(string $listId, string $email): void;

    /**
     * @param string $storeId
     * @param array $productData
     *
     * @throws MailchimpApiException
     */
    public function exportProduct(string $storeId, array $productData): void;

    /**
     * @param string $storeId
     * @param array $orderData
     *
     * @throws MailchimpApiException
     */
    public function exportOrder(string $storeId, array $orderData): void;

    /**
     * @param string $storeId
     * @param string $orderId
     *
     * @throws MailchimpApiException
     */
    public function removeOrder(string $storeId, string $orderId): void;
}
