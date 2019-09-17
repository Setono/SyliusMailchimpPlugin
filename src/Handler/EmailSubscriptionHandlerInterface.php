<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

interface EmailSubscriptionHandlerInterface
{
    /**
     * @param string $email
     * @param string|null $firstName
     * @param string|null $lastName
     */
    public function handle(string $email, ?string $firstName = null, ?string $lastName = null): void;
}
