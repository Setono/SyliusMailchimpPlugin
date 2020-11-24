<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use function Safe\sprintf;
use Sylius\Component\Core\OrderCheckoutStates;

trait OrderRepositoryTrait
{
    use MailchimpAwareRepositoryTrait;

    public function createMailchimpPendingQueryBuilder(): QueryBuilder
    {
        $alias = 'o';

        $qb = $this->_createPendingPushQueryBuilder($alias);

        return $qb
            ->andWhere(sprintf('%s.checkoutState = :checkoutState', $alias))
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
        ;
    }
}
