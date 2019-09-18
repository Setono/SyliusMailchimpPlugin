<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Factory;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface MailchimpExportFactoryInterface extends FactoryInterface
{
    public function createForMailchimpList(AudienceInterface $mailchimpList): MailchimpExportInterface;
}
