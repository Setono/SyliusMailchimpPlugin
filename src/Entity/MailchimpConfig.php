<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class MailchimpConfig implements MailchimpConfigInterface
{
    /** @var int */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string */
    protected $apiKey;

    /** @var bool */
    protected $exportAll = false;

    /** @var Collection|MailchimpListInterface[] */
    protected $lists;

    public function __construct()
    {
        $this->lists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getExportAll(): bool
    {
        return $this->exportAll;
    }

    public function setExportAll(bool $exportAll): void
    {
        $this->exportAll = $exportAll;
    }

    public function getLists(): Collection
    {
        return $this->lists;
    }

    public function setLists($lists): void
    {
        $this->lists = $lists;
    }

    public function addList(MailchimpListInterface $mailChimpList): void
    {
        if (!$this->hasList($mailChimpList)) {
            $mailChimpList->setConfig($this);
            $this->lists->add($mailChimpList);
        }
    }

    public function removeList(MailchimpListInterface $mailChimpList): void
    {
        if ($this->hasList($mailChimpList)) {
            $mailChimpList->setConfig(null);
            $this->lists->removeElement($mailChimpList);
        }
    }

    public function hasList(MailchimpListInterface $mailChimpList): bool
    {
        return $this->lists->contains($mailChimpList);
    }

    public function getListForChannelAndLocale(ChannelInterface $channel, LocaleInterface $locale): ?MailchimpListInterface
    {
        /** @var MailchimpListInterface $list */
        foreach ($this->getLists() as $list) {
            if ($list->hasChannel($channel) && $list->hasLocale($locale)) {
                return $list;
            }
        }

        return null;
    }
}
