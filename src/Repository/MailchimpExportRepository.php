<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;

class MailchimpExportRepository extends EntityRepository implements MailchimpExportRepositoryInterface
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
            ->setParameter('completedState', MailchimpExportInterface::COMPLETED_STATE)
            ->getQuery()
            ->getResult()
        ;
    }
}
