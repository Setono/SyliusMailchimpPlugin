<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\Common\Collections\Collection;

interface MailchimpExportsAwareInterface
{
    public function getMailchimpExports(): Collection;

    public function hasMailchimpExport(MailchimpExportInterface $mailchimpExport): bool;

    public function addMailchimpExport(MailchimpExportInterface $mailchimpExport): void;

    public function removeMailchimpExport(MailchimpExportInterface $mailchimpExport): void;
}
