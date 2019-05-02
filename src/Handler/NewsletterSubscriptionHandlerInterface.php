<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

interface NewsletterSubscriptionHandlerInterface
{
    public function subscribe(string $email, ?string $firstName = null, ?string $lastName = null): void;
}
