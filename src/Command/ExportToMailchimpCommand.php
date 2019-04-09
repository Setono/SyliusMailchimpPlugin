<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportToMailchimpCommand extends Command
{
    /**
     * @var CustomerNewsletterExporterInterface
     */
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
            ->setHelp('This command allows you to export all customers to Mailchimp using the Mailchimp API')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->customerNewsletterExporter->exportNotExportedCustomers();
        } catch (\Exception $exception) {
            $output->writeln(sprintf(
                '<error>%s</error>',
                $exception->getMessage()
            ));
        }
    }
}
