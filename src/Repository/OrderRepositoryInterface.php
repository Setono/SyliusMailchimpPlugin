<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Sylius\Component\Core\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

interface OrderRepositoryInterface extends BaseOrderRepositoryInterface, MailchimpAwareRepositoryInterface
{
}
