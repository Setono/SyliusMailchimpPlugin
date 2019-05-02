<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Mocker;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class MailchimpApiClientMocker implements MailchimpApiClientInterface
{
    public function isApiKeyValid(): bool
    {
        return true;
    }

    public function isAudienceIdValid(string $audienceId): bool
    {
        return true;
    }

    public function isMergeFieldsConfigured(string $audienceId, array $requiredMergeTags): bool
    {
        return true;
    }

    public function exportEmail(string $listId, string $email, array $options = []): void
    {
    }

    public function removeEmail(string $email, string $listId): void
    {
    }

    public function exportOrder(OrderInterface $order): void
    {
    }

    public function removeOrder(OrderInterface $order): void
    {
    }
}
