<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailChimpPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpExport;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpExportInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class MailChimpExportSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MailChimpExport::class);
    }

    function it_is_a_resource(): void
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_implements_mail_chimp_export_interface(): void
    {
        $this->shouldImplement(MailChimpExportInterface::class);
    }

    function it_initializes_list_collection_by_default(): void
    {
        $this->getCustomers()->shouldHaveType(Collection::class);
    }

    function its_state_is_mutable(): void
    {
        $this->setState('active');
        $this->getState()->shouldReturn('active');
    }

    function it_adds_error(): void
    {
        $this->addError('error404');
        $this->getErrors()->shouldReturn(['error404']);
    }
}
