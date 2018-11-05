<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailChimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailChimpPlugin\ApiClient\MailChimpApiClientInterface;
use Setono\SyliusMailChimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpListInterface;
use Setono\SyliusMailChimpPlugin\EventListener\CustomerNewsletterListener;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CustomerNewsletterListenerSpec extends ObjectBehavior
{
    function let(
        MailChimpApiClientInterface $mailChimpApiClient,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        EntityManagerInterface $mailChimpListManager
    ): void {
        $this->beConstructedWith(
            $mailChimpApiClient,
            $mailChimpConfigContext,
            $channelContext,
            $localeContext,
            $mailChimpListManager
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerNewsletterListener::class);
    }

    function it_unsubscribes(
        GenericEvent $event,
        CustomerInterface $customer,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
        ChannelContextInterface $channelContext,
        MailChimpConfigInterface $mailChimpConfig,
        MailChimpListInterface $mailChimpList,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpApiClientInterface $mailChimpApiClient
    ): void {
        $customer->getEmail()->willReturn('user@example.com');
        $customer->isSubscribedToNewsletter()->willReturn(false);
        $event->getSubject()->willReturn($customer);
        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocale()->willReturn($locale);
        $mailChimpList->getListId()->willReturn('test');
        $mailChimpConfig->getListForChannelAndLocale($channel, $locale)->willReturn($mailChimpList);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);

        $mailChimpApiClient->removeEmail('user@example.com', 'test')->shouldBeCalled();
        $mailChimpList->removeEmail('user@example.com')->shouldBeCalled();

        $this->manageSubscription($event);
    }

    function it_subscribes(
        GenericEvent $event,
        CustomerInterface $customer,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
        ChannelContextInterface $channelContext,
        MailChimpConfigInterface $mailChimpConfig,
        MailChimpListInterface $mailChimpList,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpApiClientInterface $mailChimpApiClient
    ): void {
        $customer->getEmail()->willReturn('user@example.com');
        $customer->isSubscribedToNewsletter()->willReturn(true);
        $event->getSubject()->willReturn($customer);
        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocale()->willReturn($locale);
        $mailChimpList->getListId()->willReturn('test');
        $mailChimpConfig->getListForChannelAndLocale($channel, $locale)->willReturn($mailChimpList);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);

        $mailChimpApiClient->exportEmail('user@example.com', 'test')->shouldBeCalled();
        $mailChimpList->addEmail('user@example.com')->shouldBeCalled();

        $this->manageSubscription($event);
    }
}
