<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\CustomerSubscriptionManagerInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

/**
 * Update mergefields, including email in case it was changed
 *
 * @see https://stackoverflow.com/a/40866813
 */
class CustomerMergefieldsUpdateHandler implements CustomerMergefieldsUpdateHandlerInterface
{
    /** @var MailchimpListRepositoryInterface */
    protected $mailchimpListRepository;

    /** @var CustomerSubscriptionManagerInterface */
    private $customerSubscriptionManager;

    public function __construct(
        MailchimpListRepositoryInterface $mailchimpListRepository,
        CustomerSubscriptionManagerInterface $customerSubscriptionManager
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->customerSubscriptionManager = $customerSubscriptionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResourceInterface $resource, string $channelCode, ?string $oldCustomerEmail = null): void
    {
        Assert::isInstanceOf($resource, CustomerInterface::class);

        /** @var CustomerInterface $customer */
        $customer = $resource;

        /** @var MailchimpListInterface $mailchimpList */
        $mailchimpLists = $this->mailchimpListRepository->findByChannelCode($channelCode);
        foreach ($mailchimpLists as $mailchimpList) {
            $this->customerSubscriptionManager->updateCustomersMergeFieldsForList(
                $mailchimpList,
                $customer,
                $oldCustomerEmail
            );
        }
    }
}
