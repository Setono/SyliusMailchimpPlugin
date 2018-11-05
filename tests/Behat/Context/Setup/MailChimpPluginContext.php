<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailChimpPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpListInterface;
use Setono\SyliusMailChimpPlugin\Repository\MailChimpConfigRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Tests\Setono\SyliusMailChimpPlugin\Behat\Service\RandomStringGeneratorInterface;

final class MailChimpPluginContext implements Context
{
    /** @var MailChimpConfigRepositoryInterface */
    private $configRepository;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var FactoryInterface */
    private $configFactory;

    /** @var RandomStringGeneratorInterface */
    private $randomStringGenerator;

    /** @var FactoryInterface */
    private $listFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        MailChimpConfigRepositoryInterface $configRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $configFactory,
        FactoryInterface $listFactory,
        RandomStringGeneratorInterface $randomStringGenerator,
        EntityManagerInterface $entityManager
    ) {
        $this->configRepository = $configRepository;
        $this->sharedStorage = $sharedStorage;
        $this->configFactory = $configFactory;
        $this->listFactory = $listFactory;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given there is already an existing MailChimp config for the store
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
                /** @var MailChimpListInterface $list */
                $list = $this->listFactory->createNew();

                $list->setListId('12345' . $i);

                $config->addList($list);
            }

            $this->entityManager->flush();
        }
    }

    private function createConfig(
        ?string $code = null,
        ?string $apiKey = null
    ): MailChimpConfigInterface {
        /** @var MailChimpConfigInterface $config */
        $config = $this->configFactory->createNew();

        $config->setCode($code ?? $this->randomStringGenerator->generate(10));
        $config->setApiKey($apiKey ?? $this->randomStringGenerator->generate(10));

        return $config;
    }

    private function saveConfig(MailChimpConfigInterface $config): void
    {
        $this->configRepository->add($config);

        $this->sharedStorage->set('config', $config);
    }
}
