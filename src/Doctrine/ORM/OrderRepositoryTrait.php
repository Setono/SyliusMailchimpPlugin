<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Sylius\Component\Core\OrderCheckoutStates;

trait OrderRepositoryTrait
{
    use PushedToMailchimpAwareRepositoryTrait;

    public function createPendingPushQueryBuilder(): QueryBuilder
    {
        $alias = 'o';

        $qb = $this->_createPendingPushQueryBuilder($alias);

        return $qb
            ->andWhere(sprintf('%s.checkoutState = :checkoutState', $alias))
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
        ;
    }
}
