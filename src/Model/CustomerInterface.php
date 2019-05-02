<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CustomerInterface as BaseCustomerInterface;

interface CustomerInterface extends BaseCustomerInterface, MailchimpExportsAwareInterface
{
    /**
     * @return Collection
     */
    public function getExportedToMailchimpLists(): Collection;

    /**
     * @param MailchimpListInterface $mailchimpList
     *
     * @return bool
     */
    public function hasExportedToMailchimpList(MailchimpListInterface $mailchimpList): bool;

    /**
     * @param Collection $exportedToMailchimpLists
     */
    public function addExportedToMailchimpList(MailchimpListInterface $mailchimpList): void;

    /**
     * @param Collection $exportedToMailchimpLists
     */
    public function removeExportedToMailchimpList(MailchimpListInterface $mailchimpList): void;
}
