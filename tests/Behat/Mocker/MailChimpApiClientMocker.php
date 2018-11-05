<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailChimpPlugin\Behat\Mocker;

use Setono\SyliusMailChimpPlugin\ApiClient\MailChimpApiClientInterface;

final class MailChimpApiClientMocker implements MailChimpApiClientInterface
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
