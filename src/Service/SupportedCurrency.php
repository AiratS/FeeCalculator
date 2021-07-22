<?php

declare(strict_types=1);

namespace App\Service;

class SupportedCurrency
{
    private array $supportedCurrencies;

    private int $defaultScale;

    public function __construct(array $supportedCurrencies, int $defaultScale)
    {
        $this->supportedCurrencies = $supportedCurrencies;
        $this->defaultScale = $defaultScale;
    }

    public function getNames(): array
    {
        return array_map(function (string $currency) {
            return strtoupper($currency);
        }, array_keys($this->supportedCurrencies));
    }

    public function isSupported(string $currency): bool
    {
        return in_array(strtoupper($currency), $this->getNames());
    }

    public function getScale(string $currency): int
    {
        $currencyData = $this->supportedCurrencies[strtolower($currency)];
        if (isset($currencyData['scale'])) {
            return $currencyData['scale'];
        }

        return $this->defaultScale;
    }
}
