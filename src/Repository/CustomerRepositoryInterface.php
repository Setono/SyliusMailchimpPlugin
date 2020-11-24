<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Sylius\Component\Core\Repository\CustomerRepositoryInterface as BaseCustomerRepositoryInterface;

interface CustomerRepositoryInterface extends BaseCustomerRepositoryInterface, MailchimpAwareRepositoryInterface
{
}
