<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exception;

final class NotSetUpException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Please set up the Mailchimp config properly first.');
    }
}
