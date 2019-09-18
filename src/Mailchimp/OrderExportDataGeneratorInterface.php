<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface OrderExportDataGeneratorInterface
{
    public function generateStoreExportData(AudienceInterface $mailchimpList): array;

    public function generateOrderExportData(OrderInterface $order, AudienceInterface $mailchimpList): array;

    public function generateOrderProductsExportData(OrderInterface $order, AudienceInterface $mailchimpList): array;
}
