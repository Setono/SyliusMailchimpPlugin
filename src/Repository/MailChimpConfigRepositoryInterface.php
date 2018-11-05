<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Repository;

use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailChimpConfigRepositoryInterface extends RepositoryInterface
{
    public function findConfig(): ?MailChimpConfigInterface;
}
