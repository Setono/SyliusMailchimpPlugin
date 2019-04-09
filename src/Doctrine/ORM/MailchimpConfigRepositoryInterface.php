<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpConfigRepositoryInterface extends RepositoryInterface
{
    /**
     * @return MailchimpConfigInterface|null
     */
    public function findConfig(): ?MailchimpConfigInterface;
}
