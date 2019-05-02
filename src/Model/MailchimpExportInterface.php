<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface MailchimpExportInterface extends ResourceInterface, TimestampableInterface
{
    public const NEW_STATE = 'new';
    public const RESTARTING_STATE = 'restarting';
    public const IN_PROGRESS_STATE = 'in_progress';
    public const COMPLETED_STATE = 'completed';
    public const FAILED_STATE = 'failed';

    /**
     * @return bool
     */
    public function canBeRestarted(): bool;

    /**
     * @return bool
     */
    public function isCompleted(): bool;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $state
     */
    public function setState(string $state): void;

    /**
     * @param MailchimpListInterface|null $mailchimpList
     */
    public function setList(?MailchimpListInterface $mailchimpList): void;

    /**
     * @return MailchimpListInterface|null
     */
    public function getList(): ?MailchimpListInterface;

    /**
     * @return Collection|CustomerInterface[]
     */
    public function getCustomers(): Collection;

    /**
     * @return bool
     */
    public function hasCustomers(): bool;

    /**
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function hasCustomer(CustomerInterface $customer): bool;

    /**
     * @param CustomerInterface $customer
     */
    public function addCustomer(CustomerInterface $customer): void;

    /**
     * @param CustomerInterface $customer
     */
    public function removeCustomer(CustomerInterface $customer): void;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @return int
     */
    public function getErrorsCount(): int;

    /**
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * @param string|null $error
     */
    public function addError(?string $error): void;

    public function clearErrors(): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getFinishedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $finishedAt
     */
    public function setFinishedAt(?\DateTimeInterface $finishedAt): void;
}
