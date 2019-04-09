<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class MailchimpList implements MailchimpListInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $listId;

    /** @var MailchimpConfigInterface|null */
    protected $config;

    /** @var MailchimpListInterface */
    protected $list;

    /** @var Collection|ChannelInterface[] */
    protected $channels;

    /** @var Collection|LocaleInterface[] */
    protected $locales;

    /** @var array */
    protected $emails = [];

    public function __construct()
    {
        $this->channels = new ArrayCollection();
        $this->locales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListId(): ?string
    {
        return $this->listId;
    }

    public function setListId($listId): void
    {
        $this->listId = $listId;
    }

    public function getConfig(): ?MailchimpConfigInterface
    {
        return $this->config;
    }

    public function setConfig(?MailchimpConfigInterface $config): void
    {
        $this->config = $config;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function getLocales(): Collection
    {
        return $this->locales;
    }

    public function addLocale(LocaleInterface $locale): void
    {
        if (!$this->hasLocale($locale)) {
            $this->locales->add($locale);
        }
    }

    public function removeLocale(LocaleInterface $locale): void
    {
        if ($this->hasLocale($locale)) {
            $this->locales->removeElement($locale);
        }
    }

    public function hasLocale(LocaleInterface $locale): bool
    {
        return $this->locales->contains($locale);
    }

    public function addEmail(string $email): void
    {
        if (!$this->hasEmail($email)) {
            $this->emails[] = $email;
        }
    }

    public function removeEmail(string $email): void
    {
        $key = array_search($email, $this->emails, true);

        if ($key === false) {
            return;
        }

        unset($this->emails[$key]);
    }

    public function hasEmail(string $email): bool
    {
        return \in_array($email, $this->emails, true);
    }
}
