<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait CustomerTrait
{
    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface", cascade={"all"}, fetch="EXTRA_LAZY", mappedBy="customers")
     */
    protected $mailchimpExports;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface", cascade={"all"}, fetch="EXTRA_LAZY", mappedBy="exportedCustomers")
     */
    protected $exportedToMailchimpLists;

    public function __construct()
    {
        $this->mailchimpExports = new ArrayCollection();
        $this->exportedToMailchimpLists = new ArrayCollection();
    }

    /**
     * @return Collection|MailchimpExportInterface[]
     */
    public function getMailchimpExports(): Collection
    {
        return $this->mailchimpExports;
    }

    public function hasMailchimpExport(MailchimpExportInterface $mailchimpExport): bool
    {
        return $this->mailchimpExports->contains($mailchimpExport);
    }

    public function addMailchimpExport(MailchimpExportInterface $mailchimpExport): void
    {
        if (!$this->hasMailchimpExport($mailchimpExport)) {
            $this->mailchimpExports->add($mailchimpExport);
        }

        if (!$mailchimpExport->hasCustomer($this)) {
            $mailchimpExport->addCustomer($this);
        }
    }

    public function removeMailchimpExport(MailchimpExportInterface $mailchimpExport): void
    {
        if ($this->hasMailchimpExport($mailchimpExport)) {
            $this->mailchimpExports->remove($mailchimpExport);
        }

        if ($mailchimpExport->hasCustomer($this)) {
            $mailchimpExport->removeCustomer($this);
        }
    }

    /**
     * @return Collection|MailchimpListInterface[]
     */
    public function getExportedToMailchimpLists(): Collection
    {
        return $this->exportedToMailchimpLists;
    }

    public function hasExportedToMailchimpList(MailchimpListInterface $mailchimpList): bool
    {
        return $this->exportedToMailchimpLists->contains($mailchimpList);
    }

    public function addExportedToMailchimpList(MailchimpListInterface $mailchimpList): void
    {
        if (!$this->hasExportedToMailchimpList($mailchimpList)) {
            $this->exportedToMailchimpLists->add($mailchimpList);
        }

        if (!$mailchimpList->hasExportedCustomer($this)) {
            $mailchimpList->addExportedCustomer($this);
        }
    }

    public function removeExportedToMailchimpList(MailchimpListInterface $mailchimpList): void
    {
        if ($this->hasExportedToMailchimpList($mailchimpList)) {
            $this->exportedToMailchimpLists->remove($mailchimpList);
        }

        if ($mailchimpList->hasExportedCustomer($this)) {
            $mailchimpList->removeExportedCustomer($this);
        }
    }

    public function getLastOrderChannelCode(?string $defaultChannelCode = null): ?string
    {
        if (0 === $this->getOrders()->count()) {
            return $defaultChannelCode;
        }

        return $this->getOrders()->last()->getChannel()->getCode();
    }

    public function getLastOrderLocaleCode(?string $defaultLocaleCode = null): ?string
    {
        if (0 === $this->getOrders()->count()) {
            return $defaultLocaleCode;
        }

        return $this->getOrders()->last()->getLocaleCode();
    }
}
