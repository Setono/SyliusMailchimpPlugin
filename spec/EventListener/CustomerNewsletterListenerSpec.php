<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\EventListener\CustomerNewsletterListener;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerNewsletterListenerSpec extends ObjectBehavior
{
    function let(
        MailchimpApiClientInterface $mailChimpApiClient,
        MailchimpConfigContextInterface $mailChimpConfigContext,
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

    function it_subscribes(
        GenericEvent $event,
        CustomerInterface $customer,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
        ChannelContextInterface $channelContext,
        MailchimpConfigInterface $mailChimpConfig,
        MailchimpListInterface $mailChimpList,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        MailchimpApiClientInterface $mailChimpApiClient
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
