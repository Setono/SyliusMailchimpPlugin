<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\DoctrineORMBatcher\Factory\BatcherFactoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomerBatch;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomers;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PushCustomersHandler implements MessageHandlerInterface
{
    /** @var BatcherFactoryInterface */
    private $batcherFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(
        BatcherFactoryInterface $batcherFactory,
        CustomerRepositoryInterface $customerRepository,
        MessageBusInterface $commandBus
    ) {
        $this->batcherFactory = $batcherFactory;
        $this->customerRepository = $customerRepository;
        $this->commandBus = $commandBus;
    }

    public function __invoke(PushCustomers $message): void
    {
        $batcher = $this->batcherFactory->createIdCollectionBatcher($this->customerRepository->createMailchimpPendingQueryBuilder());
        foreach ($batcher->getBatches() as $batch) {
            $this->commandBus->dispatch(new PushCustomerBatch($batch));
        }
    }
}
