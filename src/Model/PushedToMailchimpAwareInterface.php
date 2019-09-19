<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use DateTimeInterface;

interface PushedToMailchimpAwareInterface
{
    /**
     * Returns true if this entity ever was pushed/synced to Mailchimp
     */
    public function isPushedToMailchimp(): bool;

    /**
     * Returns the last time this entity was pushed/synced to Mailchimp
     * Returns null if the entity has never been synced
     */
    public function getPushedToMailchimp(): ?DateTimeInterface;

    /**
     * If null is given the method will set the last mailchimp sync to 'now'
     */
    public function setPushedToMailchimp(DateTimeInterface $dateTime = null): void;
}
