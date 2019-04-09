<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Context;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContext;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\Repository\MailchimpConfigRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class MailchimpConfigContextSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MailchimpConfigContext::class);
    }

    function let(
        MailchimpConfigRepository $mailChimpConfigRepository,
        RepositoryInterface $mailChimpListRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        FactoryInterface $mailChimpConfigFactory,
        FactoryInterface $mailChimpListFactory,
        EntityManagerInterface $configEntityManager
    ): void {
        $this->beConstructedWith(
            $mailChimpConfigRepository,
            $mailChimpListRepository,
            $channelContext,
            $localeContext,
            $mailChimpConfigFactory,
            $mailChimpListFactory,
            $configEntityManager
        );
    }

    function it_gets_config(
        FactoryInterface $mailChimpConfigFactory,
        MailchimpConfigInterface $config,
        FactoryInterface $mailChimpListFactory,
        MailchimpListInterface $list,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleContextInterface $localeContext,
        LocaleInterface $locale
    ): void {
        $mailChimpConfigFactory->createNew()->willReturn($config);
        $mailChimpListFactory->createNew()->willReturn($list);
        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocale()->willReturn($locale);

        $list->setConfig($config);
        $list->addChannel($channel);
        $list->addLocale($locale);

        $config->setCode('default');
        $config->addList($list);
        $config->getListForChannelAndLocale($channel, $locale)->willReturn($list);

        $this->getConfig()->shouldBeEqualTo($config);
    }
}
