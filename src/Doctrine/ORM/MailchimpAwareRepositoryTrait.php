<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use function assert;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use function Safe\sprintf;
use Setono\SyliusMailchimpPlugin\Model\MailchimpAwareInterface;

/**
 * @mixin EntityRepository
 */
trait MailchimpAwareRepositoryTrait
{
    protected function _createPendingPushQueryBuilder(string $alias = 'o'): QueryBuilder
    {
        assert($this instanceof EntityRepository);

        $qb = $this->createQueryBuilder($alias);

        return $qb
            ->andWhere(sprintf('%s.mailchimpState = :state', $alias))
            ->setParameter('state', MailchimpAwareInterface::MAILCHIMP_STATE_PENDING)
        ;
    }
}
