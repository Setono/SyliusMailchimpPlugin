<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Repository\MailchimpExportRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
        $this->mailChimpConfigContext = $mailChimpConfigContext;
        $this->mailChimpApiClient = $mailChimpApiClient;
        $this->mailChimpExportManager = $mailChimpExportManager;
        $this->mailChimpListManager = $mailChimpListManager;
    }

    public function exportNotExportedCustomers(): ?MailchimpExportInterface
    {
        $config = $this->mailChimpConfigContext->getConfig();
        $customers = $config->getExportAll() ? $this->customerRepository->findAll() : $this->customerRepository->findNonExportedCustomers();

        if (0 === count($customers)) {
            return null;
        }

        /** @var MailchimpExportInterface $export */
        $export = $this->mailChimpExportFactory->createNew();

        $export->setState(MailchimpExportInterface::IN_PROGRESS_STATE);

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
        $config = $this->mailChimpConfigContext->getConfig();
        $customer = $order->getCustomer();

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        if ($config->getExportAll() || $customer->isSubscribedToNewsletter()) {
            $globalList = $config->getListForChannelAndLocale($channel, $locale);
            $email = $customer->getEmail();

            $this->mailChimpApiClient->exportEmail($email, $globalList->getListId());

            $globalList->addEmail($email);

            $this->mailChimpListManager->flush();
        }
    }
}
