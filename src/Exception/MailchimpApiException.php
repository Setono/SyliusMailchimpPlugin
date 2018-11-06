<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exception;

final class MailchimpApiException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
