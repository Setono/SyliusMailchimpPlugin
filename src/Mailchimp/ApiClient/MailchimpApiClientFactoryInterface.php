<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient;

use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;

interface MailchimpApiClientFactoryInterface
{
    public function buildClient(MailchimpConfigInterface $mailchimpConfig): MailchimpApiClientInterface;
}
