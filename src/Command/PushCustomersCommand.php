<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PushCustomersCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-mailchimp:push-customers';

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        parent::__construct();

        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Pushes/synchronizes pending customers to Mailchimp lists. Notice this will not update customers in the ecommerce section of Mailchimp, use setono:sylius-mailchimp:push-orders for that');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $this->commandBus->dispatch(new PushCustomers());

        $this->release();

        return 0;
    }
}
