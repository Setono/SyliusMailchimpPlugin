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
     * @return Collection
     */
    public function getMailchimpExports(): Collection
    {
        return $this->mailchimpExports;
    }

    /**
     * @param MailchimpExportInterface $mailchimpExport
     *
     * @return bool
     */
    public function hasMailchimpExport(MailchimpExportInterface $mailchimpExport): bool
    {
        return $this->mailchimpExports->contains($mailchimpExport);
    }

    /**
     * @param MailchimpExportInterface $mailchimpExport
     */
    public function addMailchimpExport(MailchimpExportInterface $mailchimpExport): void
    {
        if (!$this->hasMailchimpExport($mailchimpExport)) {
            $this->mailchimpExports->add($mailchimpExport);
        }

        if (!$mailchimpExport->hasCustomer($this)) {
            $mailchimpExport->addCustomer($this);
        }
    }

    /**
     * @param MailchimpExportInterface $mailchimpExport
     */
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
     * @return Collection
     */
    public function getExportedToMailchimpLists(): Collection
    {
        return $this->exportedToMailchimpLists;
    }

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return bool
     */
    public function hasExportedToMailchimpList(MailchimpListInterface $mailchimpList): bool
    {
        return $this->exportedToMailchimpLists->contains($mailchimpList);
    }

    /**
     * @param Collection $exportedToMailchimpLists
     */
    public function addExportedToMailchimpList(MailchimpListInterface $mailchimpList): void
    {
        if (!$this->hasExportedToMailchimpList($mailchimpList)) {
            $this->exportedToMailchimpLists->add($mailchimpList);
        }

        if (!$mailchimpList->hasExportedCustomer($this)) {
            $mailchimpList->addExportedCustomer($this);
        }
    }

    /**
     * @param Collection $exportedToMailchimpLists
     */
    public function removeExportedToMailchimpList(MailchimpListInterface $mailchimpList): void
    {
        if ($this->hasExportedToMailchimpList($mailchimpList)) {
            $this->exportedToMailchimpLists->remove($mailchimpList);
        }

        if ($mailchimpList->hasExportedCustomer($this)) {
            $mailchimpList->removeExportedCustomer($this);
        }
    }
}
