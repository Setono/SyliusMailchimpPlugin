<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpListRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function findByPhrase(string $phrase): array;

    public function createByMailchimpConfigIdQueryBuilder(string $mailchimpConfigId): QueryBuilder;

    /**
     * @return MailchimpListInterface[]
     */
    public function findByChannel(ChannelInterface $channel): array;

    public function findByChannelCode(string $channelCode): array;

    /**
     * @return MailchimpListInterface[]
     */
    public function findByChannelWithStoreConfigured(ChannelInterface $channel): array;
}
