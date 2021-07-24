<?php

declare(strict_types=1);

namespace App\Service;

class SupportedCurrency
{
    private array $supportedCurrencies;
    private int $defaultScale;

    public function __construct(array $supportedCurrencies, int $defaultScale)
    {
        $this->supportedCurrencies = $this->changeCurrencyNamesToLowercase($supportedCurrencies);
        $this->defaultScale = $defaultScale;
    }

    public function isSupported(string $currency): bool
    {
        return in_array(strtolower($currency), $this->getNames());
    }

    public function getScale(string $currency): int
    {
        $currencyData = $this->supportedCurrencies[strtolower($currency)];
        if (isset($currencyData['scale'])) {
            return $currencyData['scale'];
        }

        return $this->defaultScale;
    }

    public function getNames(): array
    {
        return array_keys($this->supportedCurrencies);
    }

    private function changeCurrencyNamesToLowercase(array $supportedCurrencies): array
    {
        return array_change_key_case($supportedCurrencies, CASE_LOWER);
    }
}
