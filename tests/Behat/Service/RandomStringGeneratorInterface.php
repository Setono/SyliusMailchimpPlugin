<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Service;

interface RandomStringGeneratorInterface
{
    public function generate(int $length): string;
}
