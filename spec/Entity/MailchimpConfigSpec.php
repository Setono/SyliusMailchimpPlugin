<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfig;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class MailchimpConfigSpec extends ObjectBehavior
{
    function it_is_initializable()
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

    function its_export_all_is_mutable(): void
    {
        $this->setExportAll(true);
        $this->getExportAll()->shouldReturn(true);
    }

    function its_lists_is_mutable(Collection $lists): void
    {
        $this->setLists($lists);
        $this->getLists()->shouldReturn($lists);
    }

    function it_adds_list(MailchimpListInterface $mailChimpList): void
    {
        $mailChimpList->setConfig($this)->shouldBeCalled();

        $this->addList($mailChimpList);
        $this->hasList($mailChimpList)->shouldReturn(true);
    }

    function it_removes_list(MailchimpListInterface $mailChimpList): void
    {
        $this->addList($mailChimpList);
        $this->hasList($mailChimpList)->shouldReturn(true);

        $this->removeList($mailChimpList);
        $this->hasList($mailChimpList)->shouldReturn(false);
    }
}
