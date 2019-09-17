<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Application\Model;

use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface as SetonoSyliusMailchimpPluginCustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerTrait as SetonoSyliusMailchimpPluginCustomerTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
class Customer extends BaseCustomer implements SetonoSyliusMailchimpPluginCustomerInterface
{
    use SetonoSyliusMailchimpPluginCustomerTrait {
        SetonoSyliusMailchimpPluginCustomerTrait::__construct as private __setonoSyliusMailchimpPluginCustomerTraitConstruct;
    }

    public function __construct()
    {
        parent::__construct();
        $this->__setonoSyliusMailchimpPluginCustomerTraitConstruct();
    }
}
