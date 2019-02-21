<?php

declare(strict_types=1);

namespace BitBag\SyliusMailChimpPlugin\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Handler\NewsletterSubscriptionHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class NewsletterSubscriptionHandler implements NewsletterSubscriptionHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var MailchimpApiClientInterface */
    private $mailchimpApiClient;

    /** @var EntityManagerInterface */
    private $customerManager;

    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        EntityManagerInterface $customerManager,
        MailchimpApiClientInterface $mailchimpApiClient,
        MailchimpConfigContextInterface $mailchimpConfigContext,
        LocaleContextInterface $localeContext,
        ChannelContextInterface $channelContext
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerManager = $customerManager;
        $this->mailchimpApiClient = $mailchimpApiClient;
        $this->mailchimpConfigContext = $mailchimpConfigContext;
        $this->localeContext = $localeContext;
        $this->channelContext = $channelContext;
    }

    public function subscribe(string $email): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        if ($customer instanceof CustomerInterface) {
            $this->updateCustomer($customer);
        } else {
            $customer = $this->createNewCustomer($email);
        }

        $config = $this->mailchimpConfigContext->getConfig();
        $list = $config->getListForChannelAndLocale(
            $this->channelContext->getChannel(),
            $this->localeContext->getLocale()
        );

        Assert::notNull($list);

        $listId = $list->getListId();
        $this->mailchimpApiClient->exportEmail($email, $listId);
        $this->updateCustomer($customer);
    }


    private function createNewCustomer(string $email): CustomerInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setEmail($email);

        $this->customerRepository->add($customer);

        return $customer;
    }

    private function updateCustomer(CustomerInterface $customer): void
    {
        $customer->setSubscribedToNewsletter(true);

        $this->customerManager->flush();
    }
}
