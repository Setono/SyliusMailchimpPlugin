<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

abstract class CurrencyConverterAwareDataGenerator extends DataGenerator
{
    /** @var CurrencyConverterInterface */
    private $currencyConverter;

    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    protected function convertPrice(int $amount, string $sourceCurrencyCode, string $targetCurrencyCode): float
    {
        return round($this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode) / 100, 2);
    }
}
