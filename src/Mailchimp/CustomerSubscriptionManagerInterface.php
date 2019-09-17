<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;

interface CustomerSubscriptionManagerInterface
{
    public function subscribeCustomerToList(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): void;

    public function unsubscribeCustomerFromList(MailchimpListInterface $mailchimpList, CustomerInterface $customer): void;

    public function updateCustomersMergeFieldsForList(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $oldCustomerEmail = null): void;
}
