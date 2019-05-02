<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientFactoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckConfigsCommand extends Command
{
    protected static $defaultName = 'setono:mailchimp:check-configs';

    /** @var MailchimpApiClientFactoryInterface */
    private $mailchimpApiClientFactory;

    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var array */
    private $mergeFields;

    public function __construct(
        MailchimpListRepositoryInterface $mailchimpListRepository,
        MailchimpApiClientFactoryInterface $mailchimpApiClientFactory,
        array $mergeFields
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpApiClientFactory = $mailchimpApiClientFactory;
        $this->mergeFields = $mergeFields;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Check configs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'Config',
            'ApiKey Valid?',

            'Audience ID',
            'Audience ID Valid?',
            'MergeFields configured?',

            'Store ID',
            'Store ID Valid?',
        ]);

        /** @var MailchimpListInterface[] $mailchimpLists */
        $mailchimpLists = $this->mailchimpListRepository->findAll();
        foreach ($mailchimpLists as $mailchimpList) {
            /** @var MailchimpConfigInterface $mailchimpConfig */
            $mailchimpConfig = $mailchimpList->getConfig();
            $table->addRow([
                $mailchimpConfig->getCode(),
                $this->renderStatus($this->getApiKeyErrors($mailchimpConfig)),

                $mailchimpList->getAudienceId(),
                $this->renderStatus($this->getAudienceIdErrors($mailchimpList)),
                $this->renderErrors($this->getMergeFieldsConfigurationErrors($mailchimpList)),

                $mailchimpList->getStoreId(),
                $this->renderStatus($this->getStoreIdErrors($mailchimpList)),
            ]);
        }
        $table->setStyle('box');
        $table->render();
    }

    /**
     * @param string|array|null $errors
     * @param string $validLabel
     * @param string $invalidLabel
     *
     * @return string
     */
    private function renderStatus($errors, $validLabel = 'Valid', $invalidLabel = 'Invalid'): string
    {
        if (null === $errors) {
            return sprintf('<info>%s</info>', $validLabel);
        }

        return sprintf('<error>%s</error>', $invalidLabel);
    }

    /**
     * @param string|array|null $errors
     * @param string $validLabel
     * @param string $invalidLabel
     *
     * @return string
     */
    private function renderErrors($errors, $validLabel = 'Valid', $invalidLabel = 'Invalid'): string
    {
        if (null === $errors) {
            return sprintf('<info>%s</info>', $validLabel);
        }

        if (!is_array($errors)) {
            $errors = [$errors];
        }

        return sprintf(
            '<error>%s</error> (%s)',
            $invalidLabel,
            implode(', ', $errors)
        );
    }

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return string|null
     */
    private function getApiKeyErrors(MailchimpConfigInterface $mailchimpConfig): ?string
    {
        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpConfig);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if (!$apiClient->isApiKeyValid()) {
            return 'Key have no access';
        }

        return null;
    }

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return string
     */
    private function getAudienceIdErrors(MailchimpListInterface $mailchimpList): ?string
    {
        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if (!$apiClient->isAudienceIdExists($mailchimpList->getAudienceId())) {
            return 'Audience not exists';
        }

        return null;
    }

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return string
     */
    private function getStoreIdErrors(MailchimpListInterface $mailchimpList): ?string
    {
        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if (null === $mailchimpList->getStoreId()) {
            return null;
        }

        if (!$apiClient->isStoreIdExists($mailchimpList->getStoreId())) {
            return 'Store not exists';
        }

        return null;
    }

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return array|null
     */
    private function getMergeFieldsConfigurationErrors(MailchimpListInterface $mailchimpList): ?array
    {
        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());

            $existingMergeFields = $apiClient->getMergeFields(
                $mailchimpList->getAudienceId(),
                $this->mergeFields
            );

            if (!isset($existingMergeFields['merge_fields'])) {
                return ['Audience not found?'];
            }

            $errors = [];

            $existingMergeFieldsTags = array_map(function ($item) {
                return $item['tag'];
            }, $existingMergeFields['merge_fields']);

            foreach ($this->mergeFields as $requiredMergeField) {
                if (!in_array($requiredMergeField, $existingMergeFieldsTags)) {
                    $errors[] = sprintf(
                        '%s required, but not configured',
                        $requiredMergeField
                    );
                }
            }

            if (!$errors) {
                return null;
            }

            return $errors;
        } catch (\Exception $e) {
            return [$e->getMessage()];
        }
    }
}
