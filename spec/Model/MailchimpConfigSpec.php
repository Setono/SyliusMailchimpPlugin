<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfig;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class MailchimpConfigSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MailchimpConfig::class);
    }

    function it_is_a_resource(): void
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_implements_mail_chimp_config_interface(): void
    {
        $this->shouldImplement(MailchimpConfigInterface::class);
    }

    function it_initializes_list_collection_by_default(): void
    {
        $this->getLists()->shouldHaveType(Collection::class);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('123');
        $this->getCode()->shouldReturn('123');
    }

    function its_api_key_is_mutable(): void
    {
        $this->setApiKey('123');
        $this->getApiKey()->shouldReturn('123');
    }

    function its_export_subscribed_only_is_mutable(): void
    {
        $this->setExportSubscribedOnly(true);
        $this->isExportSubscribedOnly()->shouldReturn(true);
    }

    function it_adds_list(MailchimpListInterface $mailchimpList): void
    {
        $mailchimpList->setConfig($this)->shouldBeCalled();

        $this->addList($mailchimpList);
        $this->hasList($mailchimpList)->shouldReturn(true);
    }

    function it_removes_list(MailchimpListInterface $mailchimpList): void
    {
        $this->addList($mailchimpList);
        $this->hasList($mailchimpList)->shouldReturn(true);

        $this->removeList($mailchimpList);
        $this->hasList($mailchimpList)->shouldReturn(false);
    }
}
