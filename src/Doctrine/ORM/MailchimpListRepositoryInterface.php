<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpListRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder(): QueryBuilder;

    /**
     * @param string $phrase
     *
     * @return array
     */
    public function findByPhrase(string $phrase): array;

    /**
     * @param string $mailchimpConfigId
     *
     * @return QueryBuilder
     */
    public function createByMailchimpConfigIdQueryBuilder(string $mailchimpConfigId): QueryBuilder;

    /**
     * @param ChannelInterface $channel
     *
     * @return MailchimpListInterface[]
     */
    public function findByChannel(ChannelInterface $channel): array;

    /**
     * @param ChannelInterface $channel
     *
     * @return MailchimpListInterface[]
     */
    public function findByChannelWithStoreConfigured(ChannelInterface $channel): array;
}
