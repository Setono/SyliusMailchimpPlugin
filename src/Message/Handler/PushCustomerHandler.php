<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomer;
use Setono\SyliusMailchimpPlugin\Message\Command\SubscribeCustomerToAudience;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProviderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class PushCustomerHandler implements MessageHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var AudienceProviderInterface */
    private $audienceProvider;

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AudienceProviderInterface $audienceProvider,
        MessageBusInterface $messageBus
    ) {
        $this->customerRepository = $customerRepository;
        $this->audienceProvider = $audienceProvider;
        $this->messageBus = $messageBus;
    }

    public function __invoke(PushCustomer $message): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($message->getCustomerId());
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $audience = $this->audienceProvider->getAudienceFromCustomerOrders($customer);
        if (null === $audience) {
            $audience = $this->audienceProvider->getAudienceFromContext();
        }
        if (null === $audience) {
            // todo maybe this should fire a warning somewhere
            return;
        }

        $audienceId = $audience->getId();
        Assert::notNull($audienceId);

        $subscribeCustomerMessage = new SubscribeCustomerToAudience($audienceId, $customer->getId());
        $this->messageBus->dispatch($subscribeCustomerMessage);
    }
}
