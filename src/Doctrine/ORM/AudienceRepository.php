<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Repository\AudienceRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class AudienceRepository extends EntityRepository implements AudienceRepositoryInterface
{
    public function findOneByAudienceId(string $id): ?AudienceInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.audienceId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByChannel(ChannelInterface $channel): ?AudienceInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.channel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
