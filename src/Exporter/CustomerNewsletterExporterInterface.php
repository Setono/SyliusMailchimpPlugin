<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CustomerNewsletterExporterInterface
{
    public function exportNotExportedCustomers(): ?MailchimpExportInterface;

    public function exportSingleCustomerForOrder(OrderInterface $order): void;
}
