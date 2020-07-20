<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomer;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomerBatch;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PushCustomerBatchHandler implements MessageHandlerInterface
{
    /** @var QueryRebuilderInterface */
    private $queryRebuilder;

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        MessageBusInterface $messageBus
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->messageBus = $messageBus;
    }

    public function __invoke(PushCustomerBatch $message): void
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());

        /** @var CustomerInterface[] $customers */
        $customers = $q->getResult();

        foreach ($customers as $customer) {
            $pushCustomerMessage = new PushCustomer($customer->getId(), false);
            $this->messageBus->dispatch($pushCustomerMessage);
        }
    }
}
