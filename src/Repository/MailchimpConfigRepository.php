<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class MailchimpConfigRepository extends EntityRepository implements MailchimpConfigRepositoryInterface
{
    public function findConfig(): ?MailchimpConfigInterface
    {
        return $this->createQueryBuilder('o')
            ->setMaxResults(1)
            ->orderBy('o.id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
