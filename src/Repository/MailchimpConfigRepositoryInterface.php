<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Repository;

use Setono\SyliusMailChimpPlugin\Entity\MailchimpConfigInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpConfigRepositoryInterface extends RepositoryInterface
{
    public function findConfig(): ?MailchimpConfigInterface;
}
