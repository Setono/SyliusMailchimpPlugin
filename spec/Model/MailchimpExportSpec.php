<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExport;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class MailchimpExportSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MailchimpExport::class);
    }

    function it_is_a_resource(): void
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_implements_mailchimp_export_interface(): void
    {
        $this->shouldImplement(MailchimpExportInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_state_by_default(): void
    {
        $this->getState()->shouldReturn(MailchimpExportInterface::NEW_STATE);
    }

    function its_state_is_mutable(): void
    {
        $this->setState(MailchimpExportInterface::FAILED_STATE);
        $this->getState()->shouldReturn(MailchimpExportInterface::FAILED_STATE);
    }

    function it_has_no_list_by_default(): void
    {
        $this->getList()->shouldReturn(null);
    }

    function it_sets_list(MailchimpListInterface $mailchimpList): void
    {
        $this->setList($mailchimpList);
        $this->getList()->shouldReturn($mailchimpList);
    }

    function it_initializes_customers_collection_by_default(): void
    {
        $this->getCustomers()->shouldHaveType(Collection::class);
    }

    function it_has_customers_collection(CustomerInterface $customer1, CustomerInterface $customer2): void
    {
        $customer1->hasMailchimpExport($this)->willReturn(true);
        $customer2->hasMailchimpExport($this)->willReturn(true);
        $this->addCustomer($customer1);
        $this->addCustomer($customer2);

        $this->getCustomers()->shouldIterateAs([$customer1, $customer2]);
    }

    function it_can_add_and_remove_customers(CustomerInterface $customer): void
    {
        $customer->hasMailchimpExport($this)->willReturn(true);
        $this->addCustomer($customer);
        $this->hasCustomer($customer)->shouldReturn(true);

        $customer->hasMailchimpExport($this)->willReturn(false);
        $this->removeCustomer($customer);
        $this->hasCustomer($customer)->shouldReturn(false);
    }

    function it_initializes_errors_by_default(): void
    {
        $this->getErrors()->shouldReturn([]);
    }

    function it_adds_error(): void
    {
        $this->addError('error404');
        $this->getErrors()->shouldReturn(['error404']);
    }

    function it_returns_errors_count(): void
    {
        $this->addError('error404');
        $this->addError('error500');

        $this->getErrorsCount()->shouldReturn(2);
    }

    function it_has_no_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldReturn(null);
    }

    function its_creation_date_is_mutable(\DateTime $date): void
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date): void
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_no_finish_date_by_default(): void
    {
        $this->getFinishedAt()->shouldReturn(null);
    }

    function its_finish_date_is_mutable(\DateTime $date): void
    {
        $this->setFinishedAt($date);
        $this->getFinishedAt()->shouldReturn($date);
    }
}
