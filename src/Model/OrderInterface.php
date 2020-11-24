<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface as BaseOrderInterface;

interface OrderInterface extends BaseOrderInterface, MailchimpAwareInterface
{
}
