<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Command;

final class SubscribeCustomerToAudience
{
    /** @var int */
    private $audienceId;

    /** @var int */
    private $customerId;

    /** @var bool */
    private $pushEmailOnly;

    public function __construct(int $audienceId, int $customerId, bool $pushEmailOnly = false)
    {
        $this->audienceId = $audienceId;
        $this->customerId = $customerId;
        $this->pushEmailOnly = $pushEmailOnly;
    }

    public function getAudienceId(): int
    {
        return $this->audienceId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function isPushEmailOnly(): bool
    {
        return $this->pushEmailOnly;
    }
}
