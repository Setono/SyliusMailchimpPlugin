<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

class MailchimpExport implements MailchimpExportInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /**
     * Export state
     *
     * @var string
     */
    protected $state = self::NEW_STATE;

    /**
     * List where customers was exported to
     *
     * @var MailchimpListInterface|null
     */
    protected $list;

    /**
     * Customers exported during this export
     *
     * @var Collection|CustomerInterface[]
     */
    protected $customers;

    /**
     * Errors during export
     *
     * @var array
     */
    protected $errors = [];

    /**
     * When export was finished (succeed or failed)
     *
     * @var \DateTimeInterface|null
     */
    protected $finishedAt;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function canBeRestarted(): bool
    {
        return MailchimpExportInterface::FAILED_STATE === $this->getState();
    }

    /**
     * {@inheritdoc}
     */
    public function isCompleted(): bool
    {
        return MailchimpExportInterface::COMPLETED_STATE === $this->getState();
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
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(): ?MailchimpListInterface
    {
        return $this->list;
    }

    /**
     * {@inheritdoc}
     */
    public function setList(?MailchimpListInterface $list): void
    {
        $this->list = $list;

        if ($list instanceof MailchimpListInterface) {
            $list->addExport($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomers(): bool
    {
        return !$this->customers->isEmpty();
    }

    public function hasCustomer(CustomerInterface $customer): bool
    {
        return $this->customers->contains($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomer(CustomerInterface $customer): void
    {
        if (!$this->hasCustomer($customer)) {
            $this->customers->add($customer);
        }

        if (!$customer->hasMailchimpExport($this)) {
            $customer->addMailchimpExport($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCustomer(CustomerInterface $customer): void
    {
        if ($this->hasCustomer($customer)) {
            $this->customers->removeElement($customer);
        }

        if ($customer->hasMailchimpExport($this)) {
            $customer->removeMailchimpExport($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getErrorsCount(): int
    {
        return count($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors(): bool
    {
        return  $this->getErrorsCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function addError(?string $error): void
    {
        $this->errors[] = $error;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    /**
     * @param \DateTimeInterface|null $finishedAt
     */
    public function setFinishedAt(?\DateTimeInterface $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }
}
