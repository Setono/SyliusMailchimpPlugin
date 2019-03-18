<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Mocker;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Sylius\Component\Core\Model\OrderInterface;

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

    public function exportOrder(OrderInterface $order): void
    {
        return;
    }

    public function removeOrder(OrderInterface $order): void
    {
        return;
    }
}
