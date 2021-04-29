<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushOrders;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PushOrdersCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-mailchimp:push-orders';

    /** @var ClientInterface */
    private $client;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(ClientInterface $client, MessageBusInterface $commandBus)
    {
        parent::__construct();

        $this->client = $client;
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Pushes/synchronizes pending orders to Mailchimp')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $this->client->ping();

        $this->commandBus->dispatch(new PushOrders());

        $this->release();

        return 0;
    }
}
