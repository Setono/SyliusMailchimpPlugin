<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Command;

final class SubscribeCustomerToAudience
{
    /** @var int */
    private $audienceId;

    /** @var int */
    private $customerId;

    public function __construct(int $audienceId, int $customerId)
    {
        $this->audienceId = $audienceId;
        $this->customerId = $customerId;
    }

    public function getAudienceId(): int
    {
        return $this->audienceId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
