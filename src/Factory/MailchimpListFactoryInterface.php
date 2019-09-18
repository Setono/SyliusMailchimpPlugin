<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Factory;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface MailchimpListFactoryInterface extends FactoryInterface
{
    public function createForMailchimpConfig(MailchimpConfigInterface $mailchimpConfig): AudienceInterface;
}
