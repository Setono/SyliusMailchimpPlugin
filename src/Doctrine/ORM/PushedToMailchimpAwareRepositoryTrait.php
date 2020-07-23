<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

trait PushedToMailchimpAwareRepositoryTrait
{
    /**
     * @param string $alias
     * @param array $indexBy
     *
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    protected function _createPendingPushQueryBuilder(string $alias = 'o'): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias);

        return $qb
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull(sprintf('%s.pushedToMailchimp', $alias)),
                $qb->expr()->gt(
                    sprintf('%s.updatedAt', $alias),
                    sprintf('%s.pushedToMailchimp', $alias)
                )
            ))
        ;
    }

    public function resetPushedToMailchimp(): void
    {
        $this->createQueryBuilder('o')
            ->update()
            ->set('o.pushedToMailchimp', ':null')
            ->setParameter('null', null)
            ->getQuery()
            ->execute()
        ;
    }
}
