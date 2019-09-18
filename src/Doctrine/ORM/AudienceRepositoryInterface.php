<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface AudienceRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function createByMailchimpConfigIdQueryBuilder(string $mailchimpConfigId): QueryBuilder;

    public function findOneByAudienceId(string $id): ?AudienceInterface;

    public function findOneByChannel(ChannelInterface $channel): ?AudienceInterface;

    public function findByPhrase(string $phrase): array;

    /**
     * @return AudienceInterface[]
     */
    public function findByChannel(ChannelInterface $channel): array;

    public function findByChannelCode(string $channelCode): array;

    /**
     * @return AudienceInterface[]
     */
    public function findByChannelWithStoreConfigured(ChannelInterface $channel): array;
}
