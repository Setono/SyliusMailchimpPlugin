<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Provider;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;

interface AudienceProviderInterface
{
    public function getAudienceFromOrder(OrderInterface $order): ?AudienceInterface;

    public function getAudienceFromCustomerOrders(CustomerInterface $customer): ?AudienceInterface;
}
