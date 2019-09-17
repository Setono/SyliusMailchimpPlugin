<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Mocker;

use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;

final class MailchimpApiClientMocker implements MailchimpApiClientInterface
{
    public function isApiKeyValid(): bool
    {
        return true;
    }

    public function isListIdValid(string $listId): bool
    {
        return true;
    }

    public function isMergeFieldsConfigured(string $listId, array $requiredMergeTags): bool
    {
        return true;
    }

    public function exportEmail(string $listId, string $email, array $options = []): void
    {
    }

    public function removeEmail(string $email, string $listId): void
    {
    }

    public function exportOrder(string $storeId, array $orderData): void
    {
    }

    public function removeOrder(string $storeId, string $orderId): void
    {
    }
}
