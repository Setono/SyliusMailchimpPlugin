<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Loader\AudiencesLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadAudiencesCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-mailchimp:load-audiences';

    /** @var ClientInterface */
    private $client;

    /** @var AudiencesLoaderInterface */
    protected $audiencesLoader;

    public function __construct(ClientInterface $client, AudiencesLoaderInterface $audiencesLoader)
    {
        parent::__construct();

        $this->client = $client;
        $this->audiencesLoader = $audiencesLoader;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Loading audiences from Mailchimp')
            ->addOption(
                'preserve',
                'p',
                InputOption::VALUE_NONE,
                'Preserve audiences that no longer exists on Mailchimp\'s end'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $this->client->ping();

        $preserve = (bool) $input->getOption('preserve');
        $this->audiencesLoader->load($preserve);

        return 0;
    }
}
