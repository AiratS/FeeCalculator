<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Exception\CouldNotConvertCurrencyException;
use App\Exception\CurrencyConverterException;

class CurrencyConverter
{
    private ApiExchangeRate $apiExchangeRate;
    private Math $math;
    private string $defaultCurrency;
    private array $rates = [];

    public function __construct(ApiExchangeRate $apiExchangeRate, Math $math, string $defaultCurrency)
    {
        $this->apiExchangeRate = $apiExchangeRate;
        $this->math = $math;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @throws CouldNotConvertCurrencyException
     */
    public function convert(string $currentCurrency, string $targetCurrency, string $currentAmount): string
    {
        try {
            if ($this->defaultCurrency === $currentCurrency) {
                return $this->convertDefaultToGiven($currentAmount, $this->getCurrencyRate($targetCurrency));
            }

            return $this->convertToDefault($currentAmount, $this->getCurrencyRate($currentCurrency));
        } catch (Exception $exception) {
            throw new CouldNotConvertCurrencyException('Could not convert currency', 0, $exception);
        }
    }

    private function convertToDefault(string $amount, string $rate): string
    {
        return $this->math->divide($amount, $rate);
    }

    private function convertDefaultToGiven(string $amount, string $rate): string
    {
        return $this->math->multiply($amount, $rate);
    }

    /**
     * @throws CurrencyConverterException
     */
    private function getCurrencyRate(string $currency): string
    {
        $rates = $this->getRates();

        if (!isset($rates[$currency])) {
            throw new CurrencyConverterException(sprintf('The given currency "%s" is not found in rates.', $currency));
        }

        return (string) $rates[$currency];
    }

    /**
     * @throws CurrencyConverterException
     */
    private function getRates(): array
    {
        try {
            if (empty($this->rates)) {
                $this->rates = $this->apiExchangeRate->getExchangeRateData();
            }

            return $this->rates;
        } catch (Exception $exception) {
            throw new CurrencyConverterException('Could not request currency rates.', 0, $exception);
        }
    }
}
