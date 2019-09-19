<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Application\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\OrderRepositoryInterface as SetonoSyliusMailchimpPluginOrderRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\OrderRepositoryTrait as SetonoSyliusMailchimpPluginOrderRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;

class OrderRepository extends BaseOrderRepository implements SetonoSyliusMailchimpPluginOrderRepositoryInterface
{
    use SetonoSyliusMailchimpPluginOrderRepositoryTrait;
}
