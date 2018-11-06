<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

interface MailchimpApiClientInterface
{
    public function exportEmail(string $email, string $listId): void;

    public function removeEmail(string $mail, string $listId): void;
}
