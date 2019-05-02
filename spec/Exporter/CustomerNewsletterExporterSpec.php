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
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
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
        FactoryInterface $mailchimpExportFactory,
        MailchimpExportRepositoryInterface $mailchimpExportRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        RepositoryInterface $localeRepository,
        MailchimpConfigContextInterface $mailchimpConfigContext,
        MailchimpApiClientInterface $mailchimpApiClient,
        EntityManagerInterface $mailchimpExportManager,
        EntityManagerInterface $mailchimpListManager
    ): void {
        $this->beConstructedWith(
            $mailchimpExportFactory,
            $mailchimpExportRepository,
            $customerRepository,
            $channelContext,
            $localeContext,
            $localeRepository,
            $mailchimpConfigContext,
            $mailchimpApiClient,
            $mailchimpExportManager,
            $mailchimpListManager
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerNewsletterExporter::class);
    }

    function it_exports_not_exported_customers(
        MailchimpConfigContextInterface $mailchimpConfigContext,
        MailchimpConfigInterface $mailchimpConfig,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        FactoryInterface $mailchimpExportFactory,
        MailchimpExportInterface $mailchimpExport,
        OrderInterface $order,
        ChannelInterface $channel,
        LocaleInterface $locale,
        RepositoryInterface $localeRepository
    ): void {
        $mailchimpExportFactory->createNew()->willReturn($mailchimpExport);
        $mailchimpConfig->isExportSubscribedOnly()->willReturn(false);
        $mailchimpConfigContext->getConfig()->willReturn($mailchimpConfig);
        $mailchimpConfigContext->isFullySetUp()->willReturn(true);
        $customerRepository->findNotExportedSubscribers()->willReturn([$customer])->shouldBeCalled();
        $customer->getOrders()->willReturn(new ArrayCollection([$order->getWrappedObject()]));
        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('en_US');
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);

        $this->exportNotExportedCustomers();
    }

    function it_exports_single_customer_for_order(
        OrderInterface $order,
        MailchimpConfigContextInterface $mailchimpConfigContext,
        MailchimpConfigInterface $mailchimpConfig,
        ChannelInterface $channel,
        RepositoryInterface $localeRepository,
        LocaleInterface $locale,
        CustomerInterface $customer,
        MailchimpListInterface $mailchimpList
    ): void {
        $customer->getEmail()->willReturn('user@example.com');
        $order->getCustomer()->willReturn($customer);
        $mailchimpConfig->isExportSubscribedOnly()->willReturn(true);
        $mailchimpList->getListId()->willReturn('test');
        $mailchimpConfig->getListForChannelAndLocale($channel, $locale)->willReturn($mailchimpList);
        $mailchimpConfigContext->getConfig()->willReturn($mailchimpConfig);
        $mailchimpConfigContext->isFullySetUp()->willReturn(true);
        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('en_US');
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);

        $this->exportSingleCustomerForOrder($order);
    }
}
