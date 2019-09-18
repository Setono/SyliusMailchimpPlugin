<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\Audience;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class MailchimpListSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(Audience::class);
    }

    function it_is_a_resource(): void
    {
        $this->shouldHaveType(ResourceInterface::class);
    }

    function it_implements_mailchimp_list_interface(): void
    {
        $this->shouldImplement(AudienceInterface::class);
    }

    function it_implements_channels_aware_interface(): void
    {
        $this->shouldImplement(ChannelsAwareInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Default list');
        $this->getName()->shouldReturn('Default list');
    }

    function it_has_no_config_by_default(): void
    {
        $this->getConfig()->shouldReturn(null);
    }

    function it_sets_config(MailchimpConfigInterface $mailchimpConfig): void
    {
        $this->setConfig($mailchimpConfig);
        $this->getConfig()->shouldReturn($mailchimpConfig);
    }

    function it_has_no_listid_by_default(): void
    {
        $this->getListId()->shouldReturn(null);
    }

    function its_listid_is_mutable(): void
    {
        $this->setListId('1234567890');
        $this->getListId()->shouldReturn('1234567890');
    }

    function it_sets_export_subscribed_only_by_default(): void
    {
        $this->isExportSubscribedOnly()->shouldReturn(true);
    }

    function its_export_subscribed_only_is_mutable(): void
    {
        $this->setExportSubscribedOnly(false);
        $this->isExportSubscribedOnly()->shouldReturn(false);
    }

    function it_allow_customer_be_exported_if_subscribed_only(CustomerInterface $customer): void
    {
        $this->setExportSubscribedOnly(true);

        $customer->isSubscribedToNewsletter()->willReturn(true);
        $this->isCustomerExportable($customer)->shouldReturn(true);

        $customer->isSubscribedToNewsletter()->willReturn(false);
        $this->isCustomerExportable($customer)->shouldReturn(false);
    }

    function it_allow_customer_be_exported_if_not_subscribed_only(CustomerInterface $customer): void
    {
        $this->setExportSubscribedOnly(false);

        $customer->isSubscribedToNewsletter()->willReturn(true);
        $this->isCustomerExportable($customer)->shouldReturn(true);

        $customer->isSubscribedToNewsletter()->willReturn(false);
        $this->isCustomerExportable($customer)->shouldReturn(true);
    }

    function it_has_no_storeid_by_default(): void
    {
        $this->getStoreId()->shouldReturn(null);
    }

    function its_storeid_is_mutable(): void
    {
        $this->setListId('store_id');
        $this->getListId()->shouldReturn('store_id');
    }

    function it_has_no_store_currency_by_default(): void
    {
        $this->getStoreCurrency()->shouldReturn(null);
    }

    function it_sets_store_currency(CurrencyInterface $currency): void
    {
        $this->setStoreCurrency($currency);
        $this->getStoreCurrency()->shouldReturn($currency);
    }

    function it_returns_store_currency_code(CurrencyInterface $currency): void
    {
        $currency->getCode()->willReturn('UAH');
        $this->setStoreCurrency($currency);
        $this->getStoreCurrencyCode()->shouldReturn('UAH');
    }

    function it_initializes_channels_collection_by_default(): void
    {
        $this->getChannels()->shouldHaveType(Collection::class);
    }

    function it_has_channels_collection(ChannelInterface $firstChannel, ChannelInterface $secondChannel): void
    {
        $this->addChannel($firstChannel);
        $this->addChannel($secondChannel);

        $this->getChannels()->shouldIterateAs([$firstChannel, $secondChannel]);
    }

    function it_can_add_and_remove_channels(ChannelInterface $channel): void
    {
        $this->addChannel($channel);
        $this->hasChannel($channel)->shouldReturn(true);

        $this->removeChannel($channel);
        $this->hasChannel($channel)->shouldReturn(false);
    }

    function it_initializes_exports_collection_by_default(): void
    {
        $this->getExports()->shouldHaveType(Collection::class);
    }

    function it_has_exports_collection(MailchimpExportInterface $mailchimpExport1, MailchimpExportInterface $mailchimpExport2): void
    {
        $this->addExport($mailchimpExport1);
        $this->addExport($mailchimpExport2);

        $this->getExports()->shouldIterateAs([$mailchimpExport1, $mailchimpExport2]);
    }

    function it_can_add_and_remove_exports(MailchimpExportInterface $mailchimpExport): void
    {
        $this->addExport($mailchimpExport);
        $this->hasExport($mailchimpExport)->shouldReturn(true);

        $this->removeExport($mailchimpExport);
        $this->hasExport($mailchimpExport)->shouldReturn(false);
    }

    function it_initializes_exported_customers_collection_by_default(): void
    {
        $this->getExportedCustomers()->shouldHaveType(Collection::class);
    }

    function it_has_exported_customers_collection(CustomerInterface $customer1, CustomerInterface $customer2): void
    {
        $customer1->hasExportedToMailchimpList($this)->willReturn(true);
        $customer2->hasExportedToMailchimpList($this)->willReturn(true);
        $this->addExportedCustomer($customer1);
        $this->addExportedCustomer($customer2);

        $this->getExportedCustomers()->shouldIterateAs([$customer1, $customer2]);
    }

    function it_can_add_and_remove_exported_customers(CustomerInterface $customer): void
    {
        $customer->hasExportedToMailchimpList($this)->willReturn(true);
        $this->addExportedCustomer($customer);
        $this->hasExportedCustomer($customer)->shouldReturn(true);

        $customer->hasExportedToMailchimpList($this)->willReturn(false);
        $this->removeExportedCustomer($customer);
        $this->hasExportedCustomer($customer)->shouldReturn(false);
    }
}
