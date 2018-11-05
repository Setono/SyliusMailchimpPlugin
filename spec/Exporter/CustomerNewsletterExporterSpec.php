<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailChimpPlugin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailChimpPlugin\ApiClient\MailChimpApiClientInterface;
use Setono\SyliusMailChimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpExportInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpListInterface;
use Setono\SyliusMailChimpPlugin\Exporter\CustomerNewsletterExporter;
use Setono\SyliusMailChimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailChimpPlugin\Repository\MailChimpExportRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class CustomerNewsletterExporterSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $mailChimpExportFactory,
        MailChimpExportRepositoryInterface $mailChimpExportRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpApiClientInterface $mailChimpApiClient,
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
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpConfigInterface $mailChimpConfig,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        FactoryInterface $mailChimpExportFactory,
        MailChimpExportInterface $mailChimpExport,
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
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpConfigInterface $mailChimpConfig,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
        CustomerInterface $customer,
        MailChimpListInterface $mailChimpList
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
