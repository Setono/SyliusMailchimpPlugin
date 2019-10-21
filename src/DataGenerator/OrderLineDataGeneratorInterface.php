<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Setono\SyliusMailchimpPlugin\DTO\OrderLineData;
use Sylius\Component\Core\Model\OrderItemInterface;

interface OrderLineDataGeneratorInterface extends DataGeneratorInterface
{
    public function generate(OrderItemInterface $orderItem): OrderLineData;
}
