<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface AudienceRepositoryInterface extends RepositoryInterface
{
    public function findOneByAudienceId(string $id): ?AudienceInterface;

    public function findOneByChannel(ChannelInterface $channel): ?AudienceInterface;

    /**
     * Removes all audiences except ones having audienceId from $audienceIds list
     *
     * @param string[] $audienceIds
     */
    public function removeAllExceptAudienceIds(array $audienceIds): void;
}
