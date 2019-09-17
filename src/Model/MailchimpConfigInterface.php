<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpConfigInterface extends ResourceInterface, CodeAwareInterface
{
    public function getId(): ?int;

    public function getApiKey(): ?string;

    public function setApiKey(string $apiKey): void;

    public function hasList(MailchimpListInterface $mailchimpList): bool;

    public function getLists(): Collection;

    public function addList(MailchimpListInterface $mailchimpList): void;

    public function removeList(MailchimpListInterface $mailchimpList): void;
}
