<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Exporter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporter;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Repository\MailchimpExportRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerNewsletterExporterSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $mailChimpExportFactory,
        MailchimpExportRepositoryInterface $mailChimpExportRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        RepositoryInterface $localeRepository,
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
            $localeRepository,
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
        OrderInterface $order,
        ChannelInterface $channel,
        LocaleInterface $locale,
        RepositoryInterface $localeRepository
    ): void {
        $mailChimpExportFactory->createNew()->willReturn($mailChimpExport);
        $mailChimpConfig->getExportAll()->willReturn(false);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);
        $mailChimpConfigContext->isFullySetUp()->willReturn(true);
        $customerRepository->findNonExportedCustomers()->willReturn([$customer])->shouldBeCalled();
        $customer->getOrders()->willReturn(new ArrayCollection([$order->getWrappedObject()]));
        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('en_US');
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);

        $this->exportNotExportedCustomers();
    }

    function it_exports_single_customer_for_order(
        OrderInterface $order,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        MailchimpConfigInterface $mailChimpConfig,
        ChannelInterface $channel,
        RepositoryInterface $localeRepository,
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
        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('en_US');
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);

        $mailChimpList->addEmail('user@example.com')->shouldBeCalled();

        $this->exportSingleCustomerForOrder($order);
    }
}
