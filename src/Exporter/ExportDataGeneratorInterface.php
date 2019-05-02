<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ExportDataGeneratorInterface
{
    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return array
     */
    public function generateStoreExportData(MailchimpListInterface $mailchimpList): array;

    /**
     * @param OrderInterface $order
     * @param MailchimpListInterface $mailchimpList
     *
     * @return array
     */
    public function generateOrderExportData(OrderInterface $order, MailchimpListInterface $mailchimpList): array;

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function generateOrderProductsExportData(OrderInterface $order, MailchimpListInterface $mailchimpList): array;
}
