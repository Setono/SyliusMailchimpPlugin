<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class MailchimpExport implements MailchimpExportInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /** @var string */
    protected $state = self::NEW_STATE;

    /** @var Collection|CustomerInterface[] */
    protected $customers;

    /** @var array */
    protected $errors = [];

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(CustomerInterface $customer): void
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
        }
    }

    public function removeCustomer(CustomerInterface $customer): void
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(?string $error): void
    {
        $this->errors[] = $error;
    }
}
