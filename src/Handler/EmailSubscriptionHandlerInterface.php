<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

interface EmailSubscriptionHandlerInterface
{
    public function handle(string $email, ?string $firstName = null, ?string $lastName = null): void;
}
