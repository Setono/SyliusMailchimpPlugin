<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpConfigInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getCode(): ?string;

    public function setCode(?string $code): void;

    public function getStoreId(): ?string;

    public function setStoreId(string $storeId): void;

    public function getLists(): Collection;

    public function setLists($lists): void;

    public function getApiKey(): ?string;

    public function setApiKey(string $apiKey): void;

    public function getExportAll(): bool;

    public function setExportAll(bool $exportAll): void;

    public function addList(MailchimpListInterface $mailChimpList): void;

    public function removeList(MailchimpListInterface $mailChimpList): void;

    public function hasList(MailchimpListInterface $mailChimpList): bool;

    public function getListForChannelAndLocale(ChannelInterface $channel, LocaleInterface $locale): ?MailchimpListInterface;

    public function hasListForChannelAndLocale(ChannelInterface $channel, LocaleInterface $locale): bool;
}
