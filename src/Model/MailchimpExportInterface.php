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

    public function canBeRestarted(): bool;

    public function isCompleted(): bool;

    public function getState(): string;

    public function setState(string $state): void;

    public function setList(?MailchimpListInterface $mailchimpList): void;

    public function getList(): ?MailchimpListInterface;

    /**
     * @return Collection|CustomerInterface[]
     */
    public function getCustomers(): Collection;

    public function hasCustomers(): bool;

    public function hasCustomer(CustomerInterface $customer): bool;

    public function addCustomer(CustomerInterface $customer): void;

    public function removeCustomer(CustomerInterface $customer): void;

    public function getErrors(): array;

    public function getErrorsCount(): int;

    public function hasErrors(): bool;

    public function addError(?string $error): void;

    public function clearErrors(): void;

    public function getFinishedAt(): ?\DateTimeInterface;

    public function setFinishedAt(?\DateTimeInterface $finishedAt): void;
}
