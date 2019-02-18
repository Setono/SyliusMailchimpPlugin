<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use Sylius\Component\Core\Model\OrderInterface;

interface MailchimpApiClientInterface
{
    public function exportEmail(string $email, string $listId): void;

    public function removeEmail(string $mail, string $listId): void;

    public function exportOrder(OrderInterface $order): void;

    public function removeOrder(OrderInterface $order): void;
}
