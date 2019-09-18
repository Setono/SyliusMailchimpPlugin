<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\CustomerSubscriptionManagerInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

class CustomerSubscriptionHandler implements CustomerSubscriptionHandlerInterface
{
    /** @var AudienceRepositoryInterface */
    protected $mailchimpListRepository;

    /** @var EntityManagerInterface */
    protected $mailchimpListManager;

    /** @var CustomerSubscriptionManagerInterface */
    private $customerSubscriptionManager;

    public function __construct(
        AudienceRepositoryInterface $mailchimpListRepository,
        EntityManagerInterface $mailchimpListManager,
        CustomerSubscriptionManagerInterface $customerSubscriptionManager
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpListManager = $mailchimpListManager;
        $this->customerSubscriptionManager = $customerSubscriptionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResourceInterface $resource, string $channelCode, string $localeCode): void
    {
        Assert::isInstanceOf($resource, CustomerInterface::class);

        /** @var CustomerInterface $customer */
        $customer = $resource;

        /** @var AudienceInterface $mailchimpList */
        $mailchimpLists = $this->mailchimpListRepository->findByChannelCode($channelCode);
        foreach ($mailchimpLists as $mailchimpList) {
            if ($mailchimpList->isCustomerExportable($customer)) {
                $this->customerSubscriptionManager->subscribeCustomerToList(
                    $mailchimpList,
                    $customer,
                    $channelCode,
                    $localeCode
                );
            } else {
                $this->customerSubscriptionManager->unsubscribeCustomerFromList(
                    $mailchimpList,
                    $customer
                );
            }
        }

        $this->mailchimpListManager->flush();
    }
}
