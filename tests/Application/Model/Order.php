<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Application\Model;

use Sylius\Component\Core\Model\Order as BaseOrder;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface as SetonoSyliusMailchimpPluginOrderInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderTrait as SetonoSyliusMailchimpPluginOrderTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
class Order extends BaseOrder implements SetonoSyliusMailchimpPluginOrderInterface
{
    use SetonoSyliusMailchimpPluginOrderTrait;
}
