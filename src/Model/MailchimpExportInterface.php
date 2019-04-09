<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface MailchimpExportInterface extends ResourceInterface, TimestampableInterface
{
    public const NEW_STATE = 'new';
    public const IN_PROGRESS_STATE = 'in_progress';
    public const COMPLETED_STATE = 'completed';
    public const FAILED_STATE = 'failed';

    public function getState(): string;

    public function setState(string $state): void;

    public function getCustomers(): Collection;

    public function addCustomer(CustomerInterface $customer): void;

    public function removeCustomer(CustomerInterface $customer): void;

    public function getErrors(): array;

    public function addError(?string $error): void;
}
