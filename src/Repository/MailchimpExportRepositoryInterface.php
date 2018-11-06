<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface MailchimpExportRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function isCustomerExported(CustomerInterface $customer): bool;
}
