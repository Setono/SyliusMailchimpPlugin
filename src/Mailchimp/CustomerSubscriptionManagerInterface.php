<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;

interface CustomerSubscriptionManagerInterface
{
    /**
     * @param MailchimpListInterface $mailchimpList
     * @param CustomerInterface $customer
     * @param string|null $channelCode
     * @param string|null $localeCode
     */
    public function subscribeCustomerToList(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): void;

    /**
     * @param MailchimpListInterface $mailchimpList
     * @param CustomerInterface $customer
     */
    public function unsubscribeCustomerFromList(MailchimpListInterface $mailchimpList, CustomerInterface $customer): void;

    /**
     * @param MailchimpListInterface $mailchimpList
     * @param CustomerInterface $customer
     * @param string|null $oldCustomerEmail
     */
    public function updateCustomersMergeFieldsForList(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $oldCustomerEmail = null): void;
}
