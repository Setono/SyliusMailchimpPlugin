<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailChimpPlugin\ApiClient\MailChimpApiClientInterface;
use Setono\SyliusMailChimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpExportInterface;
use Setono\SyliusMailChimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailChimpPlugin\Repository\MailChimpExportRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerNewsletterExporter implements CustomerNewsletterExporterInterface
{
    /** @var FactoryInterface */
    private $mailChimpExportFactory;

    /** @var MailChimpExportRepositoryInterface */
    private $mailChimpExportRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var MailChimpConfigContextInterface */
    private $mailChimpConfigContext;

    /** @var MailChimpApiClientInterface */
    private $mailChimpApiClient;

    /** @var EntityManagerInterface */
    private $mailChimpExportManager;

    /** @var EntityManagerInterface */
    private $mailChimpListManager;

    public function __construct(
        FactoryInterface $mailChimpExportFactory,
        MailChimpExportRepositoryInterface $mailChimpExportRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpApiClientInterface $mailChimpApiClient,
        EntityManagerInterface $mailChimpExportManager,
        EntityManagerInterface $mailChimpListManager
    ) {
        $this->mailChimpExportFactory = $mailChimpExportFactory;
        $this->mailChimpExportRepository = $mailChimpExportRepository;
        $this->customerRepository = $customerRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->mailChimpConfigContext = $mailChimpConfigContext;
        $this->mailChimpApiClient = $mailChimpApiClient;
        $this->mailChimpExportManager = $mailChimpExportManager;
        $this->mailChimpListManager = $mailChimpListManager;
    }

    public function exportNotExportedCustomers(): ?MailChimpExportInterface
    {
        $config = $this->mailChimpConfigContext->getConfig();
        $customers = $config->getExportAll() ? $this->customerRepository->findAll() : $this->customerRepository->findNonExportedCustomers();

        if (0 === count($customers)) {
            return null;
        }

        /** @var MailChimpExportInterface $export */
        $export = $this->mailChimpExportFactory->createNew();

        $export->setState(MailChimpExportInterface::IN_PROGRESS_STATE);

        $this->mailChimpExportRepository->add($export);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        foreach ($customers as $customer) {
            try {
                $globalList = $config->getListForChannelAndLocale($channel, $locale);
                $email = $customer->getEmail();

                $export->addCustomer($customer);

                $globalList->addEmail($email);

                $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());
            } catch (\Exception $exception) {
                $export->setState(MailChimpExportInterface::FAILED_STATE);
                $export->addError($exception->getMessage());

                return $export;
            }
        }

        $export->setState(MailChimpExportInterface::COMPLETED_STATE);

        $this->mailChimpExportManager->flush();

        return $export;
    }

    public function exportSingleCustomerForOrder(OrderInterface $order): void
    {
        $config = $this->mailChimpConfigContext->getConfig();
        $customer = $order->getCustomer();

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        if ($config->getExportAll()) {
            $globalList = $config->getListForChannelAndLocale($channel, $locale);
            $email = $customer->getEmail();

            $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());

            $globalList->addEmail($email);

            $this->mailChimpListManager->flush();
        }
    }
}
