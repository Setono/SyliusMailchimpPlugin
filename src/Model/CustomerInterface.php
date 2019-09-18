<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CustomerInterface as BaseCustomerInterface;

interface CustomerInterface extends BaseCustomerInterface
{
    /**
     * Returns the last time this customer was synced to Mailchimp
     * Returns null if the customer has never been synced
     */
    public function getLastMailchimpSync(): ?DateTimeInterface;

    /**
     * If null is given the method will set the last mailchimp sync to 'now'
     */
    public function setLastMailchimpSync(DateTimeInterface $dateTime = null): void;
}
