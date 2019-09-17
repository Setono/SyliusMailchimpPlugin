<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderExportManagerInterface
{
    public function exportOrder(OrderInterface $order): void;

    public function removeOrder(OrderInterface $order): void;
}
