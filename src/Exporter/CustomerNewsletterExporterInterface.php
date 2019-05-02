<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CustomerNewsletterExporterInterface
{
    /**
     * @param MailchimpExportInterface $mailchimpExport
     * @param int $limit
     *
     * @return int
     */
    public function handleExport(MailchimpExportInterface $mailchimpExport, int $limit = 100): int;

    /**
     * @param OrderInterface $order
     */
    public function exportSingleCustomerForOrder(OrderInterface $order): void;

    /**
     * @param MailchimpListInterface $mailchimpList
     * @param CustomerInterface $customer
     * @param string|null $channelCode
     * @param string|null $localeCode
     *
     * @return bool
     */
    public function exportCustomer(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): bool;
}
