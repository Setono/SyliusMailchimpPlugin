<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Sylius\Component\Core\Model\CustomerInterface as BaseCustomerInterface;

interface CustomerInterface extends BaseCustomerInterface, PushedToMailchimpAwareInterface
{
}
