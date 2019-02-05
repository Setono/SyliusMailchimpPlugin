<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporter;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Repository\MailchimpExportRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerNewsletterExporterSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $mailChimpExportFactory,
        MailchimpExportRepositoryInterface $mailChimpExportRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        MailchimpApiClientInterface $mailChimpApiClient,
        EntityManagerInterface $mailChimpExportManager,
        EntityManagerInterface $mailChimpListManager
    ): void {
        $this->beConstructedWith(
            $mailChimpExportFactory,
            $mailChimpExportRepository,
            $customerRepository,
            $channelContext,
            $localeContext,
            $mailChimpConfigContext,
            $mailChimpApiClient,
            $mailChimpExportManager,
            $mailChimpListManager
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerNewsletterExporter::class);
    }

    function it_exports_not_exported_customers(
        MailchimpConfigContextInterface $mailChimpConfigContext,
        MailchimpConfigInterface $mailChimpConfig,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        FactoryInterface $mailChimpExportFactory,
        MailchimpExportInterface $mailChimpExport,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
        LocaleInterface $locale
    ): void {
        $mailChimpExportFactory->createNew()->willReturn($mailChimpExport);
        $mailChimpConfig->getExportAll()->willReturn(false);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);
        $customerRepository->findNonExportedCustomers()->willReturn([$customer])->shouldBeCalled();
        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocale()->willReturn($locale);

        $this->exportNotExportedCustomers();
    }

    function it_exports_single_customer_for_order(
        OrderInterface $order,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        MailchimpConfigInterface $mailChimpConfig,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
        CustomerInterface $customer,
        MailchimpListInterface $mailChimpList
    ): void {
        $customer->getEmail()->willReturn('user@example.com');
        $order->getCustomer()->willReturn($customer);
        $mailChimpConfig->getExportAll()->willReturn(true);
        $mailChimpList->getListId()->willReturn('test');
        $mailChimpConfig->getListForChannelAndLocale($channel, $locale)->willReturn($mailChimpList);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);
        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocale()->willReturn($locale);

        $mailChimpList->addEmail('user@example.com')->shouldBeCalled();

        $this->exportSingleCustomerForOrder($order);
    }
}
