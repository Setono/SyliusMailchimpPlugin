<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpConfigRepositoryInterface extends RepositoryInterface
{
    /**
     * @return MailchimpConfigInterface|null
     */
    public function findConfig(): ?MailchimpConfigInterface;
}
