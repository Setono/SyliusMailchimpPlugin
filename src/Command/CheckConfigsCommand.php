<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Command;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckConfigsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-mailchimp:check-configs';

    /** @var MailchimpApiClientInterface */
    private $mailchimpApiClient;

    /** @var AudienceRepositoryInterface */
    private $mailchimpListRepository;

    /** @var array */
    private $mergeFields;

    public function __construct(
        AudienceRepositoryInterface $mailchimpListRepository,
        MailchimpApiClientInterface $mailchimpApiClient,
        array $mergeFields
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpApiClient = $mailchimpApiClient;
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

            'List ID',
            'List ID Valid?',
            'MergeFields configured?',

            'Store ID',
            'Store ID Valid?',
        ]);

        /** @var AudienceInterface[] $mailchimpLists */
        $mailchimpLists = $this->mailchimpListRepository->findAll();
        foreach ($mailchimpLists as $mailchimpList) {
            /** @var MailchimpConfigInterface $mailchimpConfig */
            $mailchimpConfig = $mailchimpList->getConfig();
            $table->addRow([
                $mailchimpConfig->getCode(),
                $this->renderStatus($this->getApiKeyErrors($mailchimpConfig)),

                $mailchimpList->getAudienceId(),
                $this->renderStatus($this->getListIdErrors($mailchimpList)),
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

    private function getApiKeyErrors(MailchimpConfigInterface $mailchimpConfig): ?string
    {
        try {
            $apiClient = $this->mailchimpApiClient->buildClient($mailchimpConfig);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if (!$apiClient->isApiKeyValid()) {
            return 'Key have no access';
        }

        return null;
    }

    /**
     * @return string
     */
    private function getListIdErrors(AudienceInterface $mailchimpList): ?string
    {
        try {
            $apiClient = $this->mailchimpApiClient->buildClient($mailchimpList->getConfig());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if (!$apiClient->isListIdExists($mailchimpList->getAudienceId())) {
            return 'List not exists';
        }

        return null;
    }

    /**
     * @return string
     */
    private function getStoreIdErrors(AudienceInterface $mailchimpList): ?string
    {
        try {
            $apiClient = $this->mailchimpApiClient->buildClient($mailchimpList->getConfig());
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

    private function getMergeFieldsConfigurationErrors(AudienceInterface $mailchimpList): ?array
    {
        try {
            $apiClient = $this->mailchimpApiClient->buildClient($mailchimpList->getConfig());

            $existingMergeFields = $apiClient->getMergeFields(
                $mailchimpList->getAudienceId(),
                $this->mergeFields
            );

            if (!isset($existingMergeFields['merge_fields'])) {
                return ['List not found?'];
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
