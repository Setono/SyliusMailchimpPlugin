<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;

interface MailchimpApiClientFactoryInterface
{
    /**
     * @param MailchimpConfigInterface $mailchimpConfig
     *
     * @return MailchimpApiClientInterface
     */
    public function buildClient(MailchimpConfigInterface $mailchimpConfig): MailchimpApiClientInterface;
}
