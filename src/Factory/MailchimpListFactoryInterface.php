<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Factory;

use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface MailchimpListFactoryInterface extends FactoryInterface
{
    public function createForMailchimpConfig(MailchimpConfigInterface $mailchimpConfig): MailchimpListInterface;
}
