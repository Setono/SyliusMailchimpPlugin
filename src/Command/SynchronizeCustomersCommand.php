<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\DoctrineORMBatcher\Factory\BatcherFactoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\SynchronizeCustomerBatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SynchronizeCustomersCommand extends Command
{
    use LockableTrait;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var BatcherFactoryInterface */
    private $batcherFactory;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        BatcherFactoryInterface $batcherFactory,
        MessageBusInterface $commandBus
    ) {
        parent::__construct();

        $this->customerRepository = $customerRepository;
        $this->batcherFactory = $batcherFactory;
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:sylius-mailchimp:synchronize-customers')
            ->setDescription('Synchronize pending customers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $batcher = $this->batcherFactory->createIdCollectionBatcher($this->customerRepository->createPendingSyncQueryBuilder());
        foreach ($batcher->getBatches() as $batch) {
            $this->commandBus->dispatch(new SynchronizeCustomerBatch($batch));
        }

        $this->release();

        return 0;
    }
}
