<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CustomerInterface as BaseCustomerInterface;

interface CustomerInterface extends BaseCustomerInterface, MailchimpExportsAwareInterface
{
    /**
     * @return Collection|MailchimpListInterface[]
     */
    public function getExportedToMailchimpLists(): Collection;

    public function hasExportedToMailchimpList(MailchimpListInterface $mailchimpList): bool;

    public function addExportedToMailchimpList(MailchimpListInterface $mailchimpList): void;

    public function removeExportedToMailchimpList(MailchimpListInterface $mailchimpList): void;

    public function getLastOrderChannelCode(?string $defaultChannelCode = null): ?string;

    public function getLastOrderLocaleCode(?string $defaultLocaleCode = null): ?string;
}
