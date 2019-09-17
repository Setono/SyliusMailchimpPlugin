<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Factory\MailchimpExportFactoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MailchimpExportCreateCommand extends Command
{
    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var MailchimpExportFactoryInterface */
    private $mailchimpExportFactory;

    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    public function __construct(
        MailchimpListRepositoryInterface $mailchimpListRepository,
        MailchimpExportFactoryInterface $mailchimpExportFactory,
        MailchimpExportRepositoryInterface $mailchimpExportRepository
    ) {
        parent::__construct();

        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpExportFactory = $mailchimpExportFactory;
        $this->mailchimpExportRepository = $mailchimpExportRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName('setono:mailchimp:export:create')
            ->setDescription('Create new export')
            ->setHelp('This command creates new export')
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Export to which lists?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var array|null $listIds */
        $listIds = $input->getOption('list');
        if (!is_array($listIds) || empty($listIds)) {
            $mailchimpLists = $this->mailchimpListRepository->findAll();
        } else {
            $mailchimpLists = [];
            foreach ($listIds as $listId) {
                $mailchimpList = $this->mailchimpListRepository->find($listId);
                if (!$mailchimpList instanceof MailchimpListInterface) {
                    $output->writeln(sprintf('<error>List with ID %s was not found... Abort</error>', $listId));

                    return;
                }
                $mailchimpList[] = $mailchimpList;
            }
        }

        /** @var MailchimpListInterface $mailchimpList */
        foreach ($mailchimpLists as $mailchimpList) {
            $mailchimpExport = $this->mailchimpExportFactory->createForMailchimpList($mailchimpList);
            $this->mailchimpExportRepository->add($mailchimpExport);

            $output->writeln(sprintf(
                '<info>Export #%s was created for list #%s "%s"</info>',
                $mailchimpExport->getId(),
                $mailchimpList->getId(),
                $mailchimpList->getName()
            ));
        }
    }
}
