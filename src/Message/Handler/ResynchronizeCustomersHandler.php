<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\ResynchronizeCustomers;
use Setono\SyliusMailchimpPlugin\Message\Command\SynchronizeCustomers;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResynchronizeCustomersHandler implements MessageHandlerInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        MessageBusInterface $commandBus
    ) {
        $this->customerRepository = $customerRepository;
        $this->commandBus = $commandBus;
    }

    public function __invoke(ResynchronizeCustomers $message)
    {
        $this->customerRepository->resetLastMailchimpSync();

        $this->commandBus->dispatch(new SynchronizeCustomers());
    }
}
