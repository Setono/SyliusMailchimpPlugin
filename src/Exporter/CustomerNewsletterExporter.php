<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientFactoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Factory\MailchimpExportFactoryInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerNewsletterExporter implements CustomerNewsletterExporterInterface
{
    /** @var MailchimpExportFactoryInterface */
    private $mailchimpExportFactory;

    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var EntityManagerInterface */
    private $mailchimpExportManager;

    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var EntityManagerInterface */
    private $mailchimpListManager;

    /** @var MailchimpApiClientFactoryInterface */
    private $mailchimpApiClientFactory;

    /** @var array */
    private $mailchimpMergeFields;

    public function __construct(
        FactoryInterface $mailchimpExportFactory,
        MailchimpExportRepositoryInterface $mailchimpExportRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        RepositoryInterface $localeRepository,
        MailchimpApiClientFactoryInterface $mailchimpApiClientFactory,
        EntityManagerInterface $mailchimpExportManager,
        MailchimpListRepositoryInterface $mailchimpListRepository,
        EntityManagerInterface $mailchimpListManager,
        array $mailchimpMergeFields
    ) {
        $this->mailchimpExportFactory = $mailchimpExportFactory;
        $this->mailchimpExportRepository = $mailchimpExportRepository;
        $this->customerRepository = $customerRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->localeRepository = $localeRepository;
        $this->mailchimpApiClientFactory = $mailchimpApiClientFactory;
        $this->mailchimpExportManager = $mailchimpExportManager;
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpListManager = $mailchimpListManager;
        $this->mailchimpMergeFields = $mailchimpMergeFields;
    }

    /**
     * {@inheritdoc}
     */
    public function handleExport(MailchimpExportInterface $mailchimpExport, int $limit = 100): int
    {
        /** @var MailchimpListInterface $mailchimpList */
        $mailchimpList = $mailchimpExport->getList();

        $customers = $mailchimpList->isExportSubscribedOnly() ?
            $this->customerRepository->findNotExportedSubscribers($mailchimpList, $limit) :
            $this->customerRepository->findAllNotExported($mailchimpList, $limit)
        ;

        $customersExported = 0;
        $initialErrorsCount = $mailchimpExport->getErrorsCount();
        if (0 == count($customers)) {
            // Once no unexported customers found, count this export as completed
            $mailchimpExport->setState(MailchimpExportInterface::COMPLETED_STATE);
            $mailchimpExport->setFinishedAt(new \DateTime());
        } else {
            /** @var CustomerInterface $customer */
            foreach ($customers as $customer) {
                try {
                    $isCustomerExported = $this->exportCustomer($mailchimpList, $customer);
                    if ($isCustomerExported) {
                        $mailchimpExport->addCustomer($customer);
                        $customer->addMailchimpExport($mailchimpExport);
                        $mailchimpList->addExportedCustomer($customer);
                    }

                    $customersExported++;
                } catch (\Exception $exception) {
                    $mailchimpExport->addError($exception->getMessage());
                }
            }

            if ($mailchimpExport->getErrorsCount() > $initialErrorsCount) {
                $mailchimpExport->setState(MailchimpExportInterface::FAILED_STATE);
                $mailchimpExport->setFinishedAt(new \DateTime());
            } else {
                $mailchimpExport->setState(MailchimpExportInterface::IN_PROGRESS_STATE);
            }
        }

        $this->mailchimpExportRepository->add($mailchimpExport);

        return $customersExported;
    }

    /**
     * {@inheritdoc}
     */
    public function exportSingleCustomerForOrder(OrderInterface $order): void
    {
        $mailchimpLists = $this->mailchimpListRepository->findByChannel($order->getChannel());

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        foreach ($mailchimpLists as $mailchimpList) {
            if (!$mailchimpList->shouldCustomerBeExported($customer)) {
                continue;
            }

            $isCustomerExported = $this->exportCustomer(
                $mailchimpList,
                $customer,
                $this->channelContext->getChannel()->getCode(),
                $order->getLocaleCode()
            );

            if ($isCustomerExported) {
                // @todo
            }
        }

        $this->mailchimpListManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @todo OptionsBuilder
     */
    public function exportCustomer(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): bool
    {
        if (!$localeCode) {
            $localeCode = $this->resolveCustomerLocaleCode($customer);
        }

        $mergeFields = [];

        if ($this->mailchimpMergeFields['first_name'] && $customer->getFirstName()) {
            $mergeFields[$this->mailchimpMergeFields['first_name']] = $customer->getFirstName();
        }

        if ($this->mailchimpMergeFields['last_name'] && $customer->getLastName()) {
            $mergeFields[$this->mailchimpMergeFields['last_name']] = $customer->getLastName();
        }

        if ($this->mailchimpMergeFields['address']) {
            if (null !== $customer->getDefaultAddress()) {
                /** @var AddressInterface $address */
                $address = $customer->getDefaultAddress();
            } elseif (!$customer->getAddresses()->isEmpty()) {
                $address = $customer->getAddresses()->first();
            } else {
                $address = null;
            }

            if (null !== $address) {
                $mergeFields[$this->mailchimpMergeFields['address']] = sprintf(
                    '%s, %s, %s, %s',
                    $address->getCountryCode(),
                    $address->getProvinceName(),
                    $address->getCity(),
                    $address->getStreet()
                );
            }
        }

        if ($this->mailchimpMergeFields['phone']) {
            if (null !== $customer->getPhoneNumber()) {
                $mergeFields[$this->mailchimpMergeFields['phone']] = $customer->getPhoneNumber();
            } elseif (null !== $customer->getDefaultAddress()) {
                /** @var AddressInterface $address */
                $address = $customer->getDefaultAddress();
                $mergeFields[$this->mailchimpMergeFields['phone']] = $address->getPhoneNumber();
            } elseif (!$customer->getAddresses()->isEmpty()) {
                foreach ($customer->getAddresses() as $address) {
                    if (null !== $address->getPhoneNumber()) {
                        $mergeFields[$this->mailchimpMergeFields['phone']] = $address->getPhoneNumber();

                        break;
                    }
                }
            }
        }

        if ($this->mailchimpMergeFields['channel']) {
            if (!$channelCode) {
                $channelCode = $this->resolveCustomerChannelCode($customer);
            }
            $mergeFields[$this->mailchimpMergeFields['channel']] = $channelCode;
        }

        if ($this->mailchimpMergeFields['locale']) {
            $mergeFields[$this->mailchimpMergeFields['locale']] = $localeCode;
        }

        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());
        } catch (\Exception $e) {
            return false;
        }

        return $apiClient->exportEmail(
            $mailchimpList->getListId(),
            $customer->getEmail(),
            [
                'merge_fields' => $mergeFields,

                // @see https://mailchimp.com/help/view-and-edit-contact-languages/
                'language' => substr($localeCode, 0, 2),
            ]
        );
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return string
     */
    private function resolveCustomerChannelCode(CustomerInterface $customer): string
    {
        if (0 === $customer->getOrders()->count()) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel->getCode();
        }

        return $customer->getOrders()->last()->getChannel()->getCode();
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return string
     */
    private function resolveCustomerLocaleCode(CustomerInterface $customer): string
    {
        if (0 === $customer->getOrders()->count()) {
            return $this->localeContext->getLocaleCode();
        }

        return $customer->getOrders()->last()->getLocaleCode();
    }
}
