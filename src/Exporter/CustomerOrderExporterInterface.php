<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Sylius\Component\Core\Model\OrderInterface;

interface CustomerOrderExporterInterface
{
    /**
     * @param OrderInterface $order
     */
    public function exportOrder(OrderInterface $order): void;

    /**
     * @param OrderInterface $order
     */
    public function removeOrder(OrderInterface $order): void;
}
