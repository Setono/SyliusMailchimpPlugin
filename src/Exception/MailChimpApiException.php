<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Exception;

final class MailChimpApiException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
