<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Context;

use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;

interface MailchimpConfigContextInterface
{
    public const DEFAULT_CODE = 'default';

    /**
     * @return MailchimpConfigInterface
     */
    public function getConfig(): MailchimpConfigInterface;

    /**
     * @return bool
     */
    public function isFullySetUp(): bool;
}
