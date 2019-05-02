<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface as BaseCustomerRepositoryInterface;

interface CustomerRepositoryInterface extends BaseCustomerRepositoryInterface
{
    /**
     * @param string $mailchimpExportId
     *
     * @return QueryBuilder
     */
    public function createByMailchimpExportIdQueryBuilder(string $mailchimpExportId): QueryBuilder;

    /**
     * Find only subscribed customers who wasn't exported
     *
     * @param MailchimpListInterface $mailchimpList
     * @param int $limit
     *
     * @return CustomerInterface[]
     */
    public function findNotExportedSubscribers(MailchimpListInterface $mailchimpList, int $limit = 100): array;

    /**
     * Find all customers who wasn't exported
     *
     * @param MailchimpListInterface $mailchimpList
     * @param int $limit
     *
     * @return CustomerInterface[]
     */
    public function findAllNotExported(MailchimpListInterface $mailchimpList, int $limit = 100): array;
}
