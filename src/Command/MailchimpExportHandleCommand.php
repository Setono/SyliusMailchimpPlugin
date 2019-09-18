<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Factory\MailchimpExportFactoryInterface;
use Setono\SyliusMailchimpPlugin\Handler\MailchimpExportHandlerInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command designed to be executed as single instance.
 */
final class MailchimpExportHandleCommand extends Command
{
    /** @var AudienceRepositoryInterface */
    private $mailchimpListRepository;

    /** @var MailchimpExportFactoryInterface */
    private $mailchimpExportFactory;

    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    /** @var MailchimpExportHandlerInterface */
    private $mailchimpExportHandler;

    public function __construct(
        AudienceRepositoryInterface $mailchimpListRepository,
        MailchimpExportFactoryInterface $mailchimpExportFactory,
        MailchimpExportRepositoryInterface $mailchimpExportRepository,
        MailchimpExportHandlerInterface $mailchimpExportHandler
    ) {
        parent::__construct();

        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpExportFactory = $mailchimpExportFactory;
        $this->mailchimpExportRepository = $mailchimpExportRepository;
        $this->mailchimpExportHandler = $mailchimpExportHandler;
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
                'How much records to handle?',
                100
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $mailchimpExport = $this->mailchimpExportRepository->findOnePending();

        if ($mailchimpExport instanceof MailchimpExportInterface) {
            /** @var AudienceInterface $mailchimpList */
            $mailchimpList = $mailchimpExport->getList();
            $output->writeln(sprintf(
                '<info>Handling export #%s to list #%s "%s"...</info>',
                $mailchimpExport->getId(),
                $mailchimpList->getId(),
                $mailchimpList->getName()
            ));

            /** @var int $limit */
            $limit = (int) $input->getOption('limit');

            $this->mailchimpExportHandler->handle(
                $mailchimpExport,
                $limit
            );

            foreach ($mailchimpExport->getErrors() as $error) {
                $output->writeln($error);
            }
        } else {
            $output->writeln(sprintf(
                '<error>Empty queue...</error>'
            ));
        }
    }
}
