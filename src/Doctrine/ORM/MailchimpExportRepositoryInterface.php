<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpExportRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder(): QueryBuilder;

    /**
     * @param string $mailchimpListId
     *
     * @return QueryBuilder
     */
    public function createByMailchimpListIdQueryBuilder(string $mailchimpListId): QueryBuilder;

    /**
     * @return MailchimpExportInterface|null
     */
    public function findOnePending(): ?MailchimpExportInterface;
}
