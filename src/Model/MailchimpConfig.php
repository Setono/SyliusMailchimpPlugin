<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MailchimpConfig implements MailchimpConfigInterface
{
    /** @var int */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string */
    protected $apiKey;

    /** @var Collection|MailchimpListInterface[] */
    protected $lists;

    public function __construct()
    {
        $this->lists = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }

    /**
     * {@inheritdoc}
     */
    public function hasList(MailchimpListInterface $mailchimpList): bool
    {
        return $this->lists->contains($mailchimpList);
    }

    /**
     * {@inheritdoc}
     */
    public function addList(MailchimpListInterface $mailchimpList): void
    {
        if (!$this->hasList($mailchimpList)) {
            $mailchimpList->setConfig($this);
            $this->lists->add($mailchimpList);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeList(MailchimpListInterface $mailchimpList): void
    {
        if ($this->hasList($mailchimpList)) {
            $mailchimpList->setConfig(null);
            $this->lists->removeElement($mailchimpList);
        }
    }
}
