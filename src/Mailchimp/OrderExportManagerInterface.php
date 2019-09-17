<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderExportManagerInterface
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
