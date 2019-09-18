<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

interface CustomerSubscriptionManagerInterface
{
    public function subscribeCustomerToList(AudienceInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): void;

    public function unsubscribeCustomerFromList(AudienceInterface $mailchimpList, CustomerInterface $customer): void;

    public function updateCustomersMergeFieldsForList(AudienceInterface $mailchimpList, CustomerInterface $customer, ?string $oldCustomerEmail = null): void;
}
