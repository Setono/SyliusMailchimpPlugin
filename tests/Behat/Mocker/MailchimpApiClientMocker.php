<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Mocker;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;

final class MailchimpApiClientMocker implements MailchimpApiClientInterface
{
    public function exportEmail(string $email, string $listId): void
    {
        return;
    }

    public function removeEmail(string $email, string $listId): void
    {
        return;
    }
}
