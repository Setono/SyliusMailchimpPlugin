<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Command;

final class PushCustomer implements CommandInterface
{
    /** @var int */
    private $customerId;

    public function __construct(int $customerId)
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
