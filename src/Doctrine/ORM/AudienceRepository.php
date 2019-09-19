<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

class AudienceRepository extends EntityRepository implements AudienceRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByAudienceId(string $id): ?AudienceInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.audienceId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByChannel(ChannelInterface $channel): ?AudienceInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.channel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByPhrase(string $phrase): array
    {
        return $this->createQueryBuilder('o')
            ->join('o.config', 'config')
            ->andWhere('o.name LIKE :phrase OR o.listId LIKE :phrase OR o.storeId LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->addOrderBy('config.code', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function createByMailchimpConfigIdQueryBuilder(string $mailchimpConfigId): QueryBuilder
    {
        return $this->createListQueryBuilder()
            ->join('o.config', 'config')
            ->andWhere('config.id = :id')
            ->setParameter('id', $mailchimpConfigId)
            ;
    }

    public function findByChannel(ChannelInterface $channel): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByChannelCode(string $channelCode): array
    {
        return $this->createQueryBuilder('o')
            ->join('o.channels', 'channel')
            ->andWhere('channel.code = :channelCode')
            ->setParameter('channelCode', $channelCode)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByChannelWithStoreConfigured(ChannelInterface $channel): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
            ->andWhere('o.storeId IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }
}
