<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpListInterface extends ResourceInterface, ChannelsAwareInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getStoreId(): ?string;

    public function setStoreId(?string $storeId): void;

    public function getStoreCurrency(): ?CurrencyInterface;

    public function setStoreCurrency(?CurrencyInterface $storeCurrency): void;

    public function getStoreCurrencyCode(): ?string;

    public function getListId(): ?string;

    public function setListId(?string $listId): void;

    public function getConfig(): ?MailchimpConfigInterface;

    public function setConfig(?MailchimpConfigInterface $config): void;

    public function isCustomerExportable(CustomerInterface $customer): bool;

    public function isExportSubscribedOnly(): bool;

    public function setExportSubscribedOnly(bool $exportSubscribedOnly): void;

    /**
     * @return Collection|MailchimpExportInterface[]
     */
    public function getExports(): Collection;

    public function hasExports(): bool;

    public function hasExport(MailchimpExportInterface $mailchimpExport): bool;

    public function addExport(MailchimpExportInterface $mailchimpExport): void;

    public function removeExport(MailchimpExportInterface $mailchimpExport): void;

    public function getExportedCustomers(): Collection;

    public function hasExportedCustomers(): bool;

    public function hasExportedCustomer(CustomerInterface $customer): bool;

    public function addExportedCustomer(CustomerInterface $customer): void;

    public function removeExportedCustomer(CustomerInterface $customer): void;
}
