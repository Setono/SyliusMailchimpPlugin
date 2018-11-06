<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpList;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class MailchimpListSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MailchimpList::class);
    }

    function it_is_a_resource(): void
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_allows_access_via_properties(MailchimpConfigInterface $config): void
    {
        $this->setListId('123');
        $this->getListId()->shouldReturn('123');

        $this->setConfig($config);
        $this->getConfig()->shouldReturn($config);
    }

    function it_associates_channels(ChannelInterface $channel): void
    {
        $this->addChannel($channel);
        $this->hasChannel($channel)->shouldReturn(true);

        $this->removeChannel($channel);

        $this->hasChannel($channel)->shouldReturn(false);
    }

    function it_associates_locales(LocaleInterface $locale): void
    {
        $this->addLocale($locale);
        $this->hasLocale($locale)->shouldReturn(true);

        $this->removeLocale($locale);

        $this->hasLocale($locale)->shouldReturn(false);
    }

    function it_associates_emails(): void
    {
        $this->hasEmail('shop@example.com')->shouldReturn(false);

        $this->addEmail('shop@example.com');
        $this->hasEmail('shop@example.com')->shouldReturn(true);

        $this->removeEmail('shop@example.com');

        $this->hasEmail('shop@example.com')->shouldReturn(false);
    }
}
