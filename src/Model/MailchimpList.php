<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Webmozart\Assert\Assert;

class MailchimpList implements MailchimpListInterface
{
    /** @var int */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var MailchimpConfigInterface|null */
    protected $config;

    /** @var string */
    protected $listId;

    /** @var bool */
    protected $exportSubscribedOnly = true;

    /** @var string|null */
    protected $storeId;

    /** @var CurrencyInterface|null */
    protected $storeCurrency;

    /** @var Collection|ChannelInterface[] */
    protected $channels;

    /** @var Collection|MailchimpExportInterface[] */
    protected $exports;

    /** @var Collection|CustomerInterface */
    protected $exportedCustomers;

    public function __construct()
    {
        $this->channels = new ArrayCollection();
        $this->exports = new ArrayCollection();
        $this->exportedCustomers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        $config = $this->getConfig();

        Assert::notNull($config);

        return sprintf(
            '%s > %s',
            $config->getCode(),
            $this->getName()
        );
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId(?string $storeId): void
    {
        $this->storeId = $storeId;
    }

    /**
     * @return CurrencyInterface|null
     */
    public function getStoreCurrency(): ?CurrencyInterface
    {
        return $this->storeCurrency;
    }

    /**
     * @param CurrencyInterface|null $storeCurrency
     */
    public function setStoreCurrency(?CurrencyInterface $storeCurrency): void
    {
        $this->storeCurrency = $storeCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreCurrencyCode(): ?string
    {
        if (null === $this->storeCurrency) {
            return null;
        }

        return $this->storeCurrency->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getAudienceId(): ?string
    {
        return $this->getListId();
    }

    /**
     * {@inheritdoc}
     */
    public function getListId(): ?string
    {
        return $this->listId;
    }

    /**
     * {@inheritdoc}
     */
    public function setListId(?string $listId): void
    {
        $this->listId = $listId;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ?MailchimpConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(?MailchimpConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shouldCustomerBeExported(CustomerInterface $customer): bool
    {
        return !$this->isExportSubscribedOnly() || $customer->isSubscribedToNewsletter();
    }

    /**
     * {@inheritdoc}
     */
    public function isExportSubscribedOnly(): bool
    {
        return $this->exportSubscribedOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function setExportSubscribedOnly(bool $exportSubscribedOnly): void
    {
        $this->exportSubscribedOnly = $exportSubscribedOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function getExports(): Collection
    {
        return $this->exports;
    }

    /**
     * {@inheritdoc}
     */
    public function hasExports(): bool
    {
        return !$this->exports->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasExport(MailchimpExportInterface $mailchimpExport): bool
    {
        return $this->exports->contains($mailchimpExport);
    }

    /**
     * {@inheritdoc}
     */
    public function addExport(MailchimpExportInterface $mailchimpExport): void
    {
        if (!$this->hasExport($mailchimpExport)) {
            $this->exports->add($mailchimpExport);
        }

        if ($this !== $mailchimpExport->getList()) {
            $mailchimpExport->setList($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeExport(MailchimpExportInterface $mailchimpExport): void
    {
        if ($this->hasExport($mailchimpExport)) {
            $mailchimpExport->setList(null);
            $this->exports->removeElement($mailchimpExport);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExportedCustomers(): Collection
    {
        return $this->exportedCustomers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasExportedCustomers(): bool
    {
        return !$this->exportedCustomers->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasExportedCustomer(CustomerInterface $customer): bool
    {
        return $this->exportedCustomers->contains($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function addExportedCustomer(CustomerInterface $customer): void
    {
        if (!$this->hasExportedCustomer($customer)) {
            $this->exportedCustomers->add($customer);
        }

        if (!$customer->hasExportedToMailchimpList($this)) {
            $customer->addExportedToMailchimpList($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeExportedCustomer(CustomerInterface $customer): void
    {
        if ($this->hasExportedCustomer($customer)) {
            $this->exportedCustomers->removeElement($customer);
        }

        if ($customer->hasExportedToMailchimpList($this)) {
            $customer->removeExportedToMailchimpList($this);
        }
    }
}
