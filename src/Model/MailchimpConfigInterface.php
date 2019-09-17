<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MailchimpConfigInterface extends ResourceInterface, CodeAwareInterface
{
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getApiKey(): ?string;

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void;

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return bool
     */
    public function hasList(MailchimpListInterface $mailchimpList): bool;

    /**
     * @return Collection
     */
    public function getLists(): Collection;

    /**
     * @param MailchimpListInterface $mailchimpList
     */
    public function addList(MailchimpListInterface $mailchimpList): void;

    /**
     * @param MailchimpListInterface $mailchimpList
     */
    public function removeList(MailchimpListInterface $mailchimpList): void;
}
