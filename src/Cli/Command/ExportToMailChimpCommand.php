<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Cli\Command;

use Setono\SyliusMailChimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportToMailChimpCommand extends Command
{
    /** @var CustomerNewsletterExporterInterface */
    private $customerNewsletterExporter;

    public function __construct(CustomerNewsletterExporterInterface $customerNewsletterExporter)
    {
        parent::__construct();

        $this->customerNewsletterExporter = $customerNewsletterExporter;
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:mailchimp:export')
            ->setDescription('Exports all customers')
            ->setHelp('This command allows you to export all customers to MailChimp API')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->customerNewsletterExporter->exportNotExportedCustomers();

        $output->writeln('Command executed.');
    }
}
