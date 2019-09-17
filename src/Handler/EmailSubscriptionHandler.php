<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class EmailSubscriptionHandler implements EmailSubscriptionHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CustomerSubscriptionHandler */
    private $customerSubscriptionHandler;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        LocaleContextInterface $localeContext,
        ChannelContextInterface $channelContext,
        CustomerSubscriptionHandler $customerSubscriptionHandler
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->localeContext = $localeContext;
        $this->channelContext = $channelContext;
        $this->customerSubscriptionHandler = $customerSubscriptionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(string $email, ?string $firstName = null, ?string $lastName = null): void
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        if (!$customer instanceof CustomerInterface) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
            $customer->setFirstName($firstName);
            $customer->setLastName($lastName);

            // @todo Sylius Pre/PostCreate event?
        }
        $customer->setSubscribedToNewsletter(true);
        $this->customerRepository->add($customer);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $this->customerSubscriptionHandler->handle(
            $customer,
            $channel->getCode(),
            $this->localeContext->getLocaleCode()
        );
    }
}
