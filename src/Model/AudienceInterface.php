<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface AudienceInterface extends ResourceInterface, ChannelAwareInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    /**
     * This is the unique id found in the Mailchimp interface
     */
    public function getAudienceId(): ?string;

    public function setAudienceId(?string $listId): void;

    public function isCustomerExportable(CustomerInterface $customer): bool;
}
