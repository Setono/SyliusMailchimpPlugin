<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpConfigRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Tests\Setono\SyliusMailchimpPlugin\Behat\Service\RandomStringGeneratorInterface;

final class MailchimpPluginContext implements Context
{
    /** @var MailchimpConfigRepositoryInterface */
    private $configRepository;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    /** @var RandomStringGeneratorInterface */
    private $randomStringGenerator;

    /** @var FactoryInterface */
    private $listFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        MailchimpConfigRepositoryInterface $configRepository,
        SharedStorageInterface $sharedStorage,
        MailchimpConfigContextInterface $mailchimpConfigContext,
        FactoryInterface $listFactory,
        RandomStringGeneratorInterface $randomStringGenerator,
        EntityManagerInterface $entityManager
    ) {
        $this->configRepository = $configRepository;
        $this->sharedStorage = $sharedStorage;
        $this->mailchimpConfigContext = $mailchimpConfigContext;
        $this->listFactory = $listFactory;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given there is already an existing Mailchimp config for the store
     */
    public function thereIsAlreadyAnExistingMailchimpConfigForTheStore(): void
    {
        $config = $this->createConfig();

        $this->saveConfig($config);
    }

    /**
     * @Given this config has :quantity lists associated to it
     */
    public function thisConfigHasListsAssociatedToIt(int $quantity): void
    {
        $config = $this->configRepository->findConfig();
        $listCount = $config->getLists()->count();
        if ($quantity > $listCount) {
            $i = $quantity - $listCount;

            for (; $i > 0; --$i) {
                /** @var AudienceInterface $list */
                $list = $this->listFactory->createNew();

                $list->setAudienceId('12345' . $i);

                $config->addList($list);
            }

            $this->entityManager->flush();
        }
    }

    private function createConfig(
        ?string $code = null,
        ?string $apiKey = null
    ): MailchimpConfigInterface {
        $config = $this->mailchimpConfigContext->getConfig();

        $config->setCode($code ?? $this->randomStringGenerator->generate(10));
        $config->setApiKey($apiKey ?? $this->randomStringGenerator->generate(10));

        return $config;
    }

    private function saveConfig(MailchimpConfigInterface $config): void
    {
        $this->configRepository->add($config);

        $this->sharedStorage->set('config', $config);
    }
}
