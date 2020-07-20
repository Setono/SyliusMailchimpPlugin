<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

interface CustomerHandlerInterface
{
    public function subscribeCustomerToAudience(
        AudienceInterface $audience,
        CustomerInterface $customer,
        bool $pushEmailOnly = false
    ): bool;
}
