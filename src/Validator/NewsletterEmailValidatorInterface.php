<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Validator;

interface NewsletterEmailValidatorInterface
{
    public function validate(string $email): array;
}
