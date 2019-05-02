<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Context;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContext;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpConfigRepository;
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
        MailchimpConfigRepository $mailchimpConfigRepository,
        RepositoryInterface $mailchimpListRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        FactoryInterface $mailchimpConfigFactory,
        FactoryInterface $mailchimpListFactory,
        EntityManagerInterface $configEntityManager
    ): void {
        $this->beConstructedWith(
            $mailchimpConfigRepository,
            $mailchimpListRepository,
            $channelContext,
            $localeContext,
            $mailchimpConfigFactory,
            $mailchimpListFactory,
            $configEntityManager
        );
    }

    function it_gets_config(
        FactoryInterface $mailchimpConfigFactory,
        MailchimpConfigInterface $config,
        FactoryInterface $mailchimpListFactory,
        MailchimpListInterface $list,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleContextInterface $localeContext,
        LocaleInterface $locale
    ): void {
        $mailchimpConfigFactory->createNew()->willReturn($config);
        $mailchimpListFactory->createNew()->willReturn($list);
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
