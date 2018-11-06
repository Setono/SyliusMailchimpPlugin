<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Locale\Model\LocalesAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpListInterface extends ResourceInterface, ChannelsAwareInterface, LocalesAwareInterface
{
    public function getId(): ?int;

    public function getListId(): ?string;

    public function setListId($listId): void;

    public function getConfig(): ?MailchimpConfigInterface;

    public function setConfig(?MailchimpConfigInterface $config): void;

    public function addEmail(string $email): void;

    public function removeEmail(string $email): void;

    public function hasEmail(string $email): bool;
}
