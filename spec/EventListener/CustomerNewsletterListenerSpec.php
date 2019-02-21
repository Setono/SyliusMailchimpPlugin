<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\EventListener\CustomerNewsletterListener;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

final class CustomerNewsletterListenerSpec extends ObjectBehavior
{
    function let(
        CustomerRepositoryInterface $customerRepository,
        MailchimpApiClientInterface $mailChimpApiClient,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        EntityManagerInterface $mailChimpListManager,
        LoggerInterface $logger
    ): void {
        $this->beConstructedWith(
            $customerRepository,
            $mailChimpApiClient,
            $mailChimpConfigContext,
            $channelContext,
            $localeContext,
            $mailChimpListManager,
            $logger,
            ['sylius_shop_register', 'sylius_shop_account_profile_update']
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerNewsletterListener::class);
    }

    function it_subscribes(
        Request $request,
        ParameterBagInterface $parameterBag,
        PostResponseEvent $postResponseEvent,
        CustomerRepositoryInterface $customerRepository,
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
        $request->request = $parameterBag;

        $postResponseEvent->getRequest()->willReturn($request);
        $request->get('_route')->willReturn('sylius_shop_register');
        $parameterBag->all()->willReturn(['foo' => [], 'bar' => ['email' => 'user@example.com']]);
        $customerRepository->findOneBy(['email' => 'user@example.com'])->willReturn($customer);
        $customer->isSubscribedToNewsletter()->willReturn(true);
        $customer->getEmail()->willReturn('user@example.com');
        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocale()->willReturn($locale);
        $mailChimpList->getListId()->willReturn('test');
        $mailChimpConfig->getListForChannelAndLocale($channel, $locale)->willReturn($mailChimpList);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);

        $mailChimpApiClient->exportEmail('user@example.com', 'test')->shouldBeCalled();
        $mailChimpList->addEmail('user@example.com')->shouldBeCalled();

        $this->manageSubscription($postResponseEvent);
    }
}
