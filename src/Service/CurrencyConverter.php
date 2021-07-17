<?php

declare(strict_types=1);

namespace App\Service;

class CurrencyConverter
{
    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string $amount
     * @return string
     */
    public function convert(string $fromCurrency, string $toCurrency, string $amount): string
    {
        if ($fromCurrency === 'USD' && $toCurrency === 'EUR') {
            $amount = $this->toDefault($amount, 1.1497);
        }

        if ($fromCurrency === 'EUR' && $toCurrency === 'USD') {
            $amount = $this->fromDefault($amount, 1.1497);
        }

        if ($fromCurrency === 'JPY' && $toCurrency === 'EUR') {
            $amount = $this->toDefault($amount, 129.53);
        }

        if ($fromCurrency === 'EUR' && $toCurrency === 'JPY') {
            $amount = $this->fromDefault($amount, 129.53);
        }

        return (string) $amount;
    }

    private function toDefault($target, $price)
    {
        return $target / $price;
    }

    private function fromDefault($current, $price)
    {
        return $current * $price;
    }
}
