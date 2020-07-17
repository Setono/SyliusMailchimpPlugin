<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Handler\CustomerHandlerInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomerBatch;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProviderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PushCustomerBatchHandler implements MessageHandlerInterface
{
    /** @var QueryRebuilderInterface */
    private $queryRebuilder;

    /** @var AudienceProviderInterface */
    private $audienceProvider;

    /** @var CustomerHandlerInterface */
    private $customerHandler;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        AudienceProviderInterface $audienceProvider,
        CustomerHandlerInterface $customerHandler
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->audienceProvider = $audienceProvider;
        $this->customerHandler = $customerHandler;
    }

    public function __invoke(PushCustomerBatch $message): void
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());

        /** @var CustomerInterface[] $customers */
        $customers = $q->getResult();

        foreach ($customers as $customer) {
            $audience = $this->audienceProvider->getAudienceFromCustomerOrders($customer);
            if (null === $audience) {
                // todo maybe this should fire a warning somewhere
                continue;
            }

            $this->customerHandler->subscribeCustomerToAudience($audience, $customer);
        }
    }
}
