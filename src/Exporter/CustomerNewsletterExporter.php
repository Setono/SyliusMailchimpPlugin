<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Setono\SyliusMailchimpPlugin\Exception\NotSetUpException;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Repository\MailchimpExportRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomerNewsletterExporter implements CustomerNewsletterExporterInterface
{
    /** @var FactoryInterface */
    private $mailChimpExportFactory;

    /** @var MailchimpExportRepositoryInterface */
    private $mailChimpExportRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var MailchimpConfigContextInterface */
    private $mailChimpConfigContext;

    /** @var MailchimpApiClientInterface */
    private $mailChimpApiClient;

    /** @var EntityManagerInterface */
    private $mailChimpExportManager;

    /** @var EntityManagerInterface */
    private $mailChimpListManager;

    public function __construct(
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
    ) {
        $this->mailChimpExportFactory = $mailChimpExportFactory;
        $this->mailChimpExportRepository = $mailChimpExportRepository;
        $this->customerRepository = $customerRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->localeRepository = $localeRepository;
        $this->mailChimpConfigContext = $mailChimpConfigContext;
        $this->mailChimpApiClient = $mailChimpApiClient;
        $this->mailChimpExportManager = $mailChimpExportManager;
        $this->mailChimpListManager = $mailChimpListManager;
    }

    public function exportNotExportedCustomers(): ?MailchimpExportInterface
    {
        if (false === $this->mailChimpConfigContext->isFullySetUp()) {
            throw new NotSetUpException();
        }

        $config = $this->mailChimpConfigContext->getConfig();
        $customers = $config->getExportAll() ? $this->customerRepository->findAll() : $this->customerRepository->findNonExportedCustomers();

        if (0 === count($customers)) {
            return null;
        }

        /** @var MailchimpExportInterface $export */
        $export = $this->mailChimpExportFactory->createNew();

        $export->setState(MailchimpExportInterface::IN_PROGRESS_STATE);

        $this->mailChimpExportRepository->add($export);

        foreach ($customers as $customer) {
            try {
                $channel = $this->resolveCustomerChannel($customer);
                $locale = $this->resolveCustomerLocale($customer);
                /** @var MailchimpListInterface $globalList */
                $globalList = $config->getListForChannelAndLocale($channel, $locale);
                $email = $customer->getEmail();

                $export->addCustomer($customer);
                $globalList->addEmail($email);

                $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());
            } catch (\Exception $exception) {
                $export->setState(MailchimpExportInterface::FAILED_STATE);
                $export->addError($exception->getMessage());

                return $export;
            }
        }

        $export->setState(MailchimpExportInterface::COMPLETED_STATE);

        $this->mailChimpExportManager->flush();

        return $export;
    }

    public function exportSingleCustomerForOrder(OrderInterface $order): void
    {
        if (false === $this->mailChimpConfigContext->isFullySetUp()) {
            return;
        }

        $config = $this->mailChimpConfigContext->getConfig();
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        $channel = $order->getChannel();
        $locale = $this->getLocaleForCode($order->getLocaleCode());

        if ($config->getExportAll() || $customer->isSubscribedToNewsletter()) {
            /** @var MailchimpListInterface $globalList */
            $globalList = $config->getListForChannelAndLocale($channel, $locale);
            $email = $customer->getEmail();

            $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());

            $globalList->addEmail($email);

            $this->mailChimpListManager->flush();
        }
    }

    private function resolveCustomerChannel(CustomerInterface $customer): ChannelInterface
    {
        if (0 === $customer->getOrders()->count()) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel;
        }

        return $customer->getOrders()->last()->getChannel();
    }

    private function resolveCustomerLocale(CustomerInterface $customer): LocaleInterface
    {
        if (0 === $customer->getOrders()->count()) {
            return $this->localeContext->getLocale();
        }

        return $this->getLocaleForCode($customer->getOrders()->last()->getLocaleCode());
    }

    private function getLocaleForCode(string $localeCode): LocaleInterface
    {
        /** @var LocaleInterface $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);

        Assert::isInstanceOf($locale, LocaleInterface::class);

        return $locale;
    }
}
