<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;

trait OrderRepositoryTrait
{
    use PushedToMailchimpAwareRepositoryTrait;

    /**
     * @throws StringsException
     */
    public function createPendingPushQueryBuilder(): QueryBuilder
    {
        return $this->_createPendingPushQueryBuilder();
    }
}
