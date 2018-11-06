<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exception;

final class ListNotFoundException extends \Exception
{
    public function __construct(string $listCode)
    {
        parent::__construct(sprintf('Mailchimp list with %s code has not been found.', $listCode));
    }
}
