<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\DoctrineORMBatcher\Factory\BatcherFactoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushOrderBatch;
use Setono\SyliusMailchimpPlugin\Message\Command\PushOrders;
use Setono\SyliusMailchimpPlugin\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PushOrdersHandler implements MessageHandlerInterface
{
    /** @var BatcherFactoryInterface */
    private $batcherFactory;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(
        BatcherFactoryInterface $batcherFactory,
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $commandBus
    ) {
        $this->batcherFactory = $batcherFactory;
        $this->orderRepository = $orderRepository;
        $this->commandBus = $commandBus;
    }

    public function __invoke(PushOrders $message): void
    {
        $batcher = $this->batcherFactory->createIdCollectionBatcher($this->orderRepository->createMailchimpPendingQueryBuilder());
        foreach ($batcher->getBatches() as $batch) {
            $this->commandBus->dispatch(new PushOrderBatch($batch));
        }
    }
}
