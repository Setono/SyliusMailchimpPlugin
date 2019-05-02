<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpListInterface extends ResourceInterface, ChannelsAwareInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getStoreId(): ?string;

    /**
     * @param string|null $storeId
     */
    public function setStoreId(?string $storeId): void;

    /**
     * @return CurrencyInterface|null
     */
    public function getStoreCurrency(): ?CurrencyInterface;

    /**
     * @param CurrencyInterface|null $storeCurrency
     */
    public function setStoreCurrency(?CurrencyInterface $storeCurrency): void;

    /**
     * @return string|null
     */
    public function getStoreCurrencyCode(): ?string;

    /**
     * @return string|null
     */
    public function getAudienceId(): ?string;

    /**
     * @return string|null
     */
    public function getListId(): ?string;

    /**
     * @param string|null $listId
     */
    public function setListId(?string $listId): void;

    /**
     * @return MailchimpConfigInterface|null
     */
    public function getConfig(): ?MailchimpConfigInterface;

    /**
     * @param MailchimpConfigInterface|null $config
     */
    public function setConfig(?MailchimpConfigInterface $config): void;

    /**
     * @return bool
     */
    public function shouldCustomerBeExported(CustomerInterface $customer): bool;

    /**
     * @return bool
     */
    public function isExportSubscribedOnly(): bool;

    /**
     * @param bool $exportSubscribedOnly
     */
    public function setExportSubscribedOnly(bool $exportSubscribedOnly): void;

    /**
     * @return Collection|MailchimpExportInterface[]
     */
    public function getExports(): Collection;

    /**
     * @return bool
     */
    public function hasExports(): bool;

    /**
     * @param MailchimpExportInterface $mailchimpExport
     *
     * @return bool
     */
    public function hasExport(MailchimpExportInterface $mailchimpExport): bool;

    /**
     * @param MailchimpExportInterface $mailchimpExport
     */
    public function addExport(MailchimpExportInterface $mailchimpExport): void;

    /**
     * @param MailchimpExportInterface $mailchimpExport
     */
    public function removeExport(MailchimpExportInterface $mailchimpExport): void;

    /**
     * @return Collection
     */
    public function getExportedCustomers(): Collection;

    /**
     * @return bool
     */
    public function hasExportedCustomers(): bool;

    /**
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function hasExportedCustomer(CustomerInterface $customer): bool;

    /**
     * @param CustomerInterface $customer
     */
    public function addExportedCustomer(CustomerInterface $customer): void;

    /**
     * @param CustomerInterface $customer
     */
    public function removeExportedCustomer(CustomerInterface $customer): void;
}
