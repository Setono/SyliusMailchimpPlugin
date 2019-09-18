<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

trait CustomerRepositoryTrait
{
    /**
     * @param string $alias
     * @param array $indexBy
     *
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    public function createPendingSyncQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');

        return $qb
            ->andWhere('o.subscribedToNewsletter = true')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('o.lastMailchimpSync'),
                $qb->expr()->gt('o.updatedAt', 'o.lastMailchimpSync')
            ))
        ;
    }

    public function createByMailchimpExportIdQueryBuilder(string $mailchimpExportId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->join('o.mailchimpExports', 'export')
            ->andWhere('export.id = :mailchimpExportId')
            ->setParameter('mailchimpExportId', $mailchimpExportId)
            ;
    }

    public function resetLastMailchimpSync(): void
    {
        $this->createQueryBuilder('o')->update()->set('o.lastMailchimpSync', null)->getQuery()->execute();
    }

    public function findNotExportedSubscribers(AudienceInterface $mailchimpList, int $limit = 100): array
    {
        return $this->createQueryBuilder('customer')
            ->andWhere('customer.subscribedToNewsletter = true')
            ->andWhere(':mailchimpList NOT MEMBER OF customer.exportedToMailchimpLists')
            ->setParameter('mailchimpList', $mailchimpList)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllNotExported(AudienceInterface $mailchimpList, int $limit = 100): array
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
