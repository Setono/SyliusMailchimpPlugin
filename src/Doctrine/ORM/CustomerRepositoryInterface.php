<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface as BaseCustomerRepositoryInterface;

interface CustomerRepositoryInterface extends BaseCustomerRepositoryInterface
{
    /**
     * Returns a query builder with customers who are pending mailchimp sync
     */
    public function createPendingSyncQueryBuilder(): QueryBuilder;

    public function createByMailchimpExportIdQueryBuilder(string $mailchimpExportId): QueryBuilder;

    /**
     * Will reset the last mailchimp sync property on all customers
     */
    public function resetLastMailchimpSync(): void;

    /**
     * Find only subscribed customers who wasn't exported
     *
     *
     * @return CustomerInterface[]
     */
    public function findNotExportedSubscribers(AudienceInterface $mailchimpList, int $limit = 100): array;

    /**
     * Find all customers who wasn't exported
     *
     *
     * @return CustomerInterface[]
     */
    public function findAllNotExported(AudienceInterface $mailchimpList, int $limit = 100): array;
}
