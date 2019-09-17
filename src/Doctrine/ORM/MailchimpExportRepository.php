<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class MailchimpExportRepository extends EntityRepository implements MailchimpExportRepositoryInterface
{
    /**
     * {@inherotdoc}
     */
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * {@inherotdoc}
     */
    public function createByMailchimpListIdQueryBuilder(string $mailchimpListId): QueryBuilder
    {
        return $this->createListQueryBuilder()
            ->join('o.list', 'list')
            ->andWhere('list.id = :id')
            ->setParameter('id', $mailchimpListId)
            ;
    }

    /**
     * {@inherotdoc}
     */
    public function findOnePending(): ?MailchimpExportInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state IN (:states)')
            ->setParameter('states', [
                MailchimpExportInterface::NEW_STATE,
                MailchimpExportInterface::RESTARTING_STATE,
                MailchimpExportInterface::IN_PROGRESS_STATE,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
