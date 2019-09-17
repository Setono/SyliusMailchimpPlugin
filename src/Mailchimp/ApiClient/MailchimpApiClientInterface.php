<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient;

use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;

interface MailchimpApiClientInterface
{
    public function isApiKeyValid(): bool;

    public function isListIdExists(string $listId): bool;

    public function isStoreIdExists(string $storeId): bool;

    /**
     * @throws MailchimpApiException
     */
    public function getMergeFields(string $listId, array $requiredMergeTags): array;

    /**
     * @throws MailchimpApiException
     */
    public function createStore(array $storeData): void;

    /**
     * @throws MailchimpApiException
     */
    public function exportEmail(string $listId, string $email, array $options): bool;

    public function updateEmail(string $listId, string $email, array $options, ?string $oldEmail = null): bool;

    /**
     * @throws MailchimpApiException
     */
    public function removeEmail(string $listId, string $email): void;

    /**
     * @throws MailchimpApiException
     */
    public function exportProduct(string $storeId, array $productData): void;

    /**
     * @throws MailchimpApiException
     */
    public function exportOrder(string $storeId, array $orderData): void;

    /**
     * @throws MailchimpApiException
     */
    public function removeOrder(string $storeId, string $orderId): void;
}
