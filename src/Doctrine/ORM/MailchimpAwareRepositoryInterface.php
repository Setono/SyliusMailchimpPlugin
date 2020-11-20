<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

interface MailchimpAwareRepositoryInterface
{
    /**
     * Returns a query builder that represent entities that are pending to be pushed to Mailchimp
     */
    public function createMailchimpPendingQueryBuilder(): QueryBuilder;

    /**
     * If $force is set to true it will reset ALL entities, not just the ones with state equals pushed
     */
    public function resetMailchimpState(bool $force = false): void;
}
