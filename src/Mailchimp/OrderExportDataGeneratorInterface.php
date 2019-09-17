<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface OrderExportDataGeneratorInterface
{
    public function generateStoreExportData(MailchimpListInterface $mailchimpList): array;

    public function generateOrderExportData(OrderInterface $order, MailchimpListInterface $mailchimpList): array;

    public function generateOrderProductsExportData(OrderInterface $order, MailchimpListInterface $mailchimpList): array;
}
