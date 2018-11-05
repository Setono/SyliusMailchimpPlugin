<?php

/*
 * This file has been created by developers from setono.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://setono.shop and write us
 * an customer on mikolaj.krol@setono.pl.
 */

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface MailChimpExportInterface extends ResourceInterface, TimestampableInterface
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
