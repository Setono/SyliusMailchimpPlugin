<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;

interface MailchimpExportsAwareInterface
{
    /**
     * @return Collection
     */
    public function getMailchimpExports(): Collection;

    /**
     * @param MailchimpExportInterface $mailchimpExport
     *
     * @return bool
     */
    public function hasMailchimpExport(MailchimpExportInterface $mailchimpExport): bool;

    /**
     * @param MailchimpExportInterface $mailchimpExport
     */
    public function addMailchimpExport(MailchimpExportInterface $mailchimpExport): void;

    /**
     * @param MailchimpExportInterface $mailchimpExport
     */
    public function removeMailchimpExport(MailchimpExportInterface $mailchimpExport): void;
}
