<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Context;

use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;

interface MailchimpConfigContextInterface
{
    public const DEFAULT_CODE = 'default';

    /**
     * @return MailchimpConfigInterface|null
     */
    public function getConfig(): ?MailchimpConfigInterface;

    /**
     * @return bool
     */
    public function isFullySetUp(): bool;
}
