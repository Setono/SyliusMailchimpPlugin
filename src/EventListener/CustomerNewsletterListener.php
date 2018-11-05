<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailChimpPlugin\ApiClient\MailChimpApiClientInterface;
use Setono\SyliusMailChimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpListInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerNewsletterListener
{
    /** @var MailChimpApiClientInterface */
    private $mailChimpApiClient;

    /** @var MailChimpConfigContextInterface */
    private $mailChimpConfigContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var EntityManagerInterface */
    private $mailChimpListManager;

    public function __construct(
        MailChimpApiClientInterface $mailChimpApiClient,
        MailChimpConfigContextInterface $mailChimpConfigContext,
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
        $globalList = $this->getGlobalList();
        $email = $customer->getEmail();

        $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());

        $globalList->addEmail($email);
    }

    private function unsubscribe(CustomerInterface $customer): void
    {
        $globalList = $this->getGlobalList();
        $email = $customer->getEmail();

        $this->mailChimpApiClient->removeEmail($email, $globalList->getListId());

        $globalList->removeEmail($email);
    }

    private function getGlobalList(): MailChimpListInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        return $this->mailChimpConfigContext->getConfig()->getListForChannelAndLocale($channel, $locale);
    }
}
