<?php


namespace Setono\SyliusMailchimpPlugin\Exporter;


use Sylius\Component\Core\Model\OrderInterface;

interface CustomerOrderExporterInterface
{
    public function exportOrder(OrderInterface $order): void;
}