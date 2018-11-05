<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Context;

use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;

interface MailChimpConfigContextInterface
{
    public const DEFAULT_CODE = 'default';

    public function getConfig(): MailChimpConfigInterface;

    public function isFullySetUp(): bool;
}
