<?php

declare(strict_types=1);

namespace App\Service;

class CurrencyRounder
{
    private SupportedCurrency $supportedCurrency;

    public function __construct(SupportedCurrency $supportedCurrency)
    {
        $this->supportedCurrency = $supportedCurrency;
    }

    public function round(string $amount, string $currency): string
    {
        return (string) round((float) $amount, $this->supportedCurrency->getScale($currency));
    }
}
