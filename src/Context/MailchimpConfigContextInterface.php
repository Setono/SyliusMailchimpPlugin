<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Context;

use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;

interface MailchimpConfigContextInterface
{
    public const DEFAULT_CODE = 'default';

    public function getConfig(): MailchimpConfigInterface;

    public function isFullySetUp(): bool;
}
