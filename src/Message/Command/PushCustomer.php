<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Command;

final class PushCustomer implements CommandInterface
{
    /** @var int */
    private $customerId;

    /** @var bool */
    private $pushOnlyEmail;

    public function __construct(int $customerId, bool $pushOnlyEmail = false)
    {
        $this->customerId = $customerId;
        $this->pushOnlyEmail = $pushOnlyEmail;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function isPushOnlyEmail(): bool
    {
        return $this->pushOnlyEmail;
    }
}
