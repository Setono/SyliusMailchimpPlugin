<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient;

use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

interface MailchimpApiClientInterface
{
    public function isApiKeyValid(): bool;

    public function getAudiences(array $options = []): array;

    public function isListIdExists(string $listId): bool;

    public function isStoreIdExists(string $storeId): bool;

    /**
     * @throws MailchimpApiException
     */
    public function getMergeFields(string $listId, array $requiredMergeTags): array;

    /**
     * This will create/update a store within Mailchimp. It will take the audience id from
     * the audience and associate it with the data in the channel (the store in MC lingo)
     */
    public function updateStore(AudienceInterface $audience): void;

    /**
     * @throws MailchimpApiException
     *
     * @deprecated Use updateStore instead
     */
    public function createStore(array $storeData): void;

    /**
     * @throws MailchimpApiException
     */
    public function exportEmail(string $listId, string $email, array $options): bool;

    /**
     * This will update or create a member
     */
    public function updateMember(AudienceInterface $audience, CustomerInterface $customer): void;

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
