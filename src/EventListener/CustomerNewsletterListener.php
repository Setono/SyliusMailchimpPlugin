<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CustomerNewsletterListener
{
    /** @var MailchimpApiClientInterface */
    private $mailChimpApiClient;

    /** @var MailchimpConfigContextInterface */
    private $mailChimpConfigContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var EntityManagerInterface */
    private $mailChimpListManager;

    public function __construct(
        MailchimpApiClientInterface $mailChimpApiClient,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        EntityManagerInterface $mailChimpListManager
    ) {
        $this->mailChimpApiClient = $mailChimpApiClient;
        $this->mailChimpConfigContext = $mailChimpConfigContext;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->mailChimpListManager = $mailChimpListManager;
    }

    public function manageSubscription(GenericEvent $event): void
    {
        /** @var CustomerInterface $customer */
        $customer = $event->getSubject();

        false === $customer->isSubscribedToNewsletter() ? $this->unsubscribe($customer) : $this->subscribe($customer);

        $this->mailChimpListManager->flush();
    }

    private function subscribe(CustomerInterface $customer): void
    {
        /** @var MailchimpListInterface $globalList */
        $globalList = $this->getGlobalList();
        $email = $customer->getEmail();

        $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());

        $globalList->addEmail($email);
    }

    private function unsubscribe(CustomerInterface $customer): void
    {
        /** @var MailchimpListInterface $globalList */
        $globalList = $this->getGlobalList();
        $email = $customer->getEmail();

        $this->mailChimpApiClient->removeEmail($email, $globalList->getListId());

        $globalList->removeEmail($email);
    }

    private function getGlobalList(): MailchimpListInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        /** @var MailchimpConfigInterface $config */
        $config = $this->mailChimpConfigContext->getConfig();

        $globalList = $config->getListForChannelAndLocale($channel, $locale);

        Assert::notNull($globalList);

        return $globalList;
    }
}
