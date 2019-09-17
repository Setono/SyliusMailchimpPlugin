<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpConfigRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder(): QueryBuilder;

    /**
     * @param string $phrase
     *
     * @return array|MailchimpConfigInterface[]
     */
    public function findByPhrase(string $phrase): array;

    /**
     * @return MailchimpConfigInterface|null
     */
    public function findOneActive(): ?MailchimpConfigInterface;
}
