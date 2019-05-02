<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Setono\SyliusMailchimpPlugin\Factory\MailchimpExportFactoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command designed to be executed as single instance.
 */
final class MailchimpExportHandleCommand extends Command
{
    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var MailchimpExportFactoryInterface */
    private $mailchimpExportFactory;

    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    /** @var CustomerNewsletterExporterInterface */
    private $customerNewsletterExporter;

    public function __construct(
        MailchimpListRepositoryInterface $mailchimpListRepository,
        MailchimpExportFactoryInterface $mailchimpExportFactory,
        MailchimpExportRepositoryInterface $mailchimpExportRepository,
        CustomerNewsletterExporterInterface $customerNewsletterExporter
    ) {
        parent::__construct();

        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpExportFactory = $mailchimpExportFactory;
        $this->mailchimpExportRepository = $mailchimpExportRepository;
        $this->customerNewsletterExporter = $customerNewsletterExporter;

    }

    protected function configure(): void
    {
        $this
            ->setName('setono:mailchimp:export:handle')
            ->setDescription('Handle export')
            ->setHelp('This command make an actual export to mailchimp via API')
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                100
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $mailchimpExport = $this->mailchimpExportRepository->findOnePending();

        if ($mailchimpExport instanceof MailchimpExportInterface) {

            /** @var MailchimpListInterface $mailchimpList */
            $mailchimpList = $mailchimpExport->getList();
            $output->writeln(sprintf(
                '<info>Handling export #%s to list #%s "%s"...</info>',
                $mailchimpExport->getId(),
                $mailchimpList->getId(),
                $mailchimpList->getName()
            ));

            $customersExported = $this->customerNewsletterExporter->handleExport(
                $mailchimpExport,
                (int)$input->getOption('limit')
            );

            $output->writeln(sprintf(
                ' - Exported: %s customers',
                $customersExported
            ));

            if ($mailchimpExport->isCompleted()) {
                $output->writeln(' - Export marked as completed');
            }
        } else {
            $output->writeln(sprintf(
                '<error>Empty queue...</error>'
            ));
        }
    }
}
