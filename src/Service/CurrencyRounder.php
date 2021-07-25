<?php

declare(strict_types=1);

namespace App\Service;

class CurrencyRounder
{
    private SupportedCurrency $supportedCurrency;
    private Math $math;

    public function __construct(SupportedCurrency $supportedCurrency, Math $math)
    {
        $this->supportedCurrency = $supportedCurrency;
        $this->math = $math;
    }

    public function ceil(string $amount, string $currency): string
    {
        return $this->math->ceil($amount, $this->supportedCurrency->getScale($currency));
    }
}
