<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Exporter;

use Setono\SyliusMailChimpPlugin\Entity\MailChimpExportInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CustomerNewsletterExporterInterface
{
    public function exportNotExportedCustomers(): ?MailChimpExportInterface;

    public function exportSingleCustomerForOrder(OrderInterface $order): void;
}
