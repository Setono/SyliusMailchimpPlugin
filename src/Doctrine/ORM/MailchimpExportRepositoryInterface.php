<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpExportRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function createByMailchimpListIdQueryBuilder(string $mailchimpListId): QueryBuilder;

    public function findOnePending(): ?MailchimpExportInterface;
}
