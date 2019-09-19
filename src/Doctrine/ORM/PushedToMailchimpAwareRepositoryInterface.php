<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

interface PushedToMailchimpAwareRepositoryInterface
{
    /**
     * Returns a query builder with entities who are pending to be pushed to Mailchimp
     */
    public function createPendingPushQueryBuilder(): QueryBuilder;

    /**
     * Will reset the pushed to mailchimp property on all entities (i.e. set it to null)
     */
    public function resetPushedToMailchimp(): void;
}
