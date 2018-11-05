<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailChimpExportRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function isCustomerExported(CustomerInterface $customer): bool;
}
