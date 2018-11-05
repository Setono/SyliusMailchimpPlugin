<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpExportInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;

class MailChimpExportRepository extends EntityRepository implements MailChimpExportRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    public function isCustomerExported(CustomerInterface $customer): bool
    {
        return (bool) $this->createQueryBuilder('o')
            ->innerJoin('o.customers', 'customer')
            ->where('customer = :customer')
            ->andWhere('o.state = :completedState')
            ->setParameter('customer', $customer)
            ->setParameter('completedState', MailChimpExportInterface::COMPLETED_STATE)
            ->getQuery()
            ->getResult()
        ;
    }
}
