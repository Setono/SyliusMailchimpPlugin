<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;

trait CustomerRepositoryTrait
{
    /**
     * @param string $alias
     * @param array $indexBy
     *
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    public function createByMailchimpExportIdQueryBuilder(string $mailchimpExportId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->join('o.mailchimpExports', 'export')
            ->andWhere('export.id = :mailchimpExportId')
            ->setParameter('mailchimpExportId', $mailchimpExportId)
            ;
    }

    public function findNotExportedSubscribers(MailchimpListInterface $mailchimpList, int $limit = 100): array
    {
        return $this->createQueryBuilder('customer')
            ->andWhere('customer.subscribedToNewsletter = 1')
            ->andWhere(':mailchimpList NOT MEMBER OF customer.exportedToMailchimpLists')
            ->setParameter('mailchimpList', $mailchimpList)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllNotExported(MailchimpListInterface $mailchimpList, int $limit = 100): array
    {
        return $this->createQueryBuilder('customer')
            ->andWhere(':mailchimpList NOT MEMBER OF customer.exportedToMailchimpLists')
            ->setParameter('mailchimpList', $mailchimpList)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }
}
