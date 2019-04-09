<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\Repository\MailchimpConfigRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Tests\Setono\SyliusMailchimpPlugin\Behat\Service\RandomStringGeneratorInterface;

final class ExportCustomersContext implements Context
{
    /** @var MailchimpConfigRepositoryInterface */
    private $configRepository;

    /** @var FactoryInterface */
    private $configFactory;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var RandomStringGeneratorInterface */
    private $randomStringGenerator;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $mailChimpList;

    public function __construct(
        MailchimpConfigRepositoryInterface $configRepository,
        FactoryInterface $configFactory,
        SharedStorageInterface $sharedStorage,
        RandomStringGeneratorInterface $randomStringGenerator,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $mailChimpList
    ) {
        $this->configRepository = $configRepository;
        $this->configFactory = $configFactory;
        $this->sharedStorage = $sharedStorage;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->mailChimpList = $mailChimpList;
    }

    /**
     * @Given I have a Mailchimp config set up
     */
    public function iHaveAMailchimpConfigSetUp(): void
    {
        $config = $this->createConfig();

        /** @var MailchimpListInterface $list */
        $list = $this->mailChimpList->createNew();

        $list->setListId($this->randomStringGenerator->generate(10));

        $config->addList($list);

        $this->saveConfig($config);

        $this->sharedStorage->set('config', $config);
    }

    /**
     * @Given the store allows all emails to be exported
     */
    public function theStoreAllowsAllEmailsToBeExported(): void
    {
        $this->configRepository->findConfig()->setExportAll(true);
    }

    /**
     * @Given I have :count customers in my database
     */
    public function iHaveCustomersInMyDatabase(int $count): void
    {
        for ($i = 0; $i < $count; ++$i) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail(
                $this->randomStringGenerator->generate(5) . '@' .
                $this->randomStringGenerator->generate(5) . '.com'
            );
            $customer->setSubscribedToNewsletter(true);

            $this->customerRepository->add($customer);
        }
    }

    private function createConfig(
        ?string $code = null,
        ?string $apiKey = null
    ): MailchimpConfigInterface {
        /** @var MailchimpConfigInterface $config */
        $config = $this->configFactory->createNew();

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
