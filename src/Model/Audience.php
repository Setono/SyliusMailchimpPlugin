<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;

class Audience implements AudienceInterface
{
    /** @var int */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $audienceId;

    /** @var ChannelInterface */
    protected $channel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAudienceId(): ?string
    {
        return $this->audienceId;
    }

    public function setAudienceId(?string $listId): void
    {
        $this->audienceId = $listId;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function isCustomerExportable(CustomerInterface $customer): bool
    {
        return $customer->isSubscribedToNewsletter();
    }
}
