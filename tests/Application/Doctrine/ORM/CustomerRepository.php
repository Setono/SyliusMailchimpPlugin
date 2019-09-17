<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Application\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface as SetonoSyliusMailchimpPluginCustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryTrait as SetonoSyliusMailchimpPluginCustomerRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;

class CustomerRepository extends BaseCustomerRepository implements SetonoSyliusMailchimpPluginCustomerRepositoryInterface
{
    use SetonoSyliusMailchimpPluginCustomerRepositoryTrait;
}
