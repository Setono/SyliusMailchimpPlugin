<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Handler\CustomerHandlerInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomer;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProviderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class PushCustomerHandler implements MessageHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var AudienceProviderInterface */
    private $audienceProvider;

    /** @var CustomerHandlerInterface */
    private $customerHandler;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AudienceProviderInterface $audienceProvider,
        CustomerHandlerInterface $customerHandler
    ) {
        $this->customerRepository = $customerRepository;
        $this->audienceProvider = $audienceProvider;
        $this->customerHandler = $customerHandler;
    }

    public function __invoke(PushCustomer $message): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($message->getCustomerId());
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $audience = $this->audienceProvider->getAudienceFromCustomerOrders($customer);
        if (null === $audience) {
            // todo maybe this should fire a warning somewhere
            return;
        }

        $this->customerHandler->subscribeCustomerToAudience($audience, $customer, $message->isPushOnlyEmail());
    }
}
