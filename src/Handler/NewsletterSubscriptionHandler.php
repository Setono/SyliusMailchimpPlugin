<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class NewsletterSubscriptionHandler implements NewsletterSubscriptionHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;


    /** @var CustomerNewsletterExporterInterface */
    private $customerNewsletterExporter;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        MailchimpListRepositoryInterface $mailchimpListRepository,
        CustomerNewsletterExporterInterface $customerNewsletterExporter,
        LocaleContextInterface $localeContext,
        ChannelContextInterface $channelContext
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->customerNewsletterExporter = $customerNewsletterExporter;
        $this->localeContext = $localeContext;
        $this->channelContext = $channelContext;
    }

    /**
     * @todo Think what to do if already subscribed
     *
     * @param string $email
     * @param string|null $firstName
     * @param string|null $lastName
     */
    public function subscribe(string $email, ?string $firstName = null, ?string $lastName = null): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        if (!$customer instanceof CustomerInterface) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
            $customer->setFirstName($firstName);
            $customer->setLastName($lastName);
        }
        $customer->setSubscribedToNewsletter(true);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var MailchimpListInterface $mailchimpList */
        $mailchimpLists = $this->mailchimpListRepository->findByChannel($channel);
        foreach ($mailchimpLists as $mailchimpList) {
            $this->customerNewsletterExporter->exportCustomer(
                $mailchimpList,
                $customer,
                $channel->getCode(),
                $this->localeContext->getLocaleCode()
            );
        }
        $this->customerRepository->add($customer);
    }
}
