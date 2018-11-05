<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Repository;

use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class MailChimpConfigRepository extends EntityRepository implements MailChimpConfigRepositoryInterface
{
    public function findConfig(): ?MailChimpConfigInterface
    {
        return $this->createQueryBuilder('o')
            ->setMaxResults(1)
            ->orderBy('o.id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
