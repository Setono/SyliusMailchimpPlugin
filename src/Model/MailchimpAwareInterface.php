<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use DateTimeInterface;

interface MailchimpAwareInterface
{
    public const MAILCHIMP_STATE_PENDING = 'pending';

    public const MAILCHIMP_STATE_PROCESSING = 'processing';

    public const MAILCHIMP_STATE_FAILED = 'failed';

    /**
     * Describes a state where the entity failed so many times that it won't be retried
     */
    public const MAILCHIMP_STATE_TERMINALLY_FAILED = 'terminally_failed';

    /**
     * Means that the entity was pushed to Mailchimp, i.e. succeeded
     */
    public const MAILCHIMP_STATE_PUSHED = 'pushed';

    public function getMailchimpState(): string;

    public function setMailchimpState(string $state): void;

    public function getMailchimpError(): ?string;

    public function setMailchimpError(?string $error): void;

    /**
     * If there's never been a state change this method returns null
     */
    public function getMailchimpStateUpdatedAt(): ?DateTimeInterface;

    public function setMailchimpStateUpdatedAt(DateTimeInterface $updatedAt): void;

    /**
     * The number of times it was tried to push the entity to Mailchimp
     */
    public function getMailchimpTries(): int;

    public function setMailchimpTries(int $tries): void;

    /**
     * This will increment the number of tries by 1
     */
    public function incrementMailchimpTries(): void;
}
