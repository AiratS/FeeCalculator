<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Exception\CouldNotConvertConvertCurrencyException;
use App\Exception\CurrencyConverterException;

class CurrencyConverter
{
    /**
     * In the free version of the API, exchange rates are shown only against the dollar
     */
    const BASE_CURRENCY = 'USD';

    /**
     * @var ApiCurrencyLayer
     */
    private ApiCurrencyLayer $apiCurrencyLayer;

    /**
     * @var Math
     */
    private Math $math;

    /**
     * @var array
     */
    private array $rates = [];

    /**
     * @param ApiCurrencyLayer $apiCurrencyLayer
     * @param Math $math
     */
    public function __construct(ApiCurrencyLayer $apiCurrencyLayer, Math $math)
    {
        $this->apiCurrencyLayer = $apiCurrencyLayer;
        $this->math = $math;
    }

    /**
     * @param string $currentCurrency
     * @param string $targetCurrency
     * @param string $currentAmount
     * @return string
     * @throws CouldNotConvertConvertCurrencyException
     */
    public function convert(string $currentCurrency, string $targetCurrency, string $currentAmount): string
    {
        try {
            if ($this->math->equals($currentAmount, '0.0')) {
                return '0.0';
            }

            $currentBaseRate = $this->getBaseRate($currentCurrency);
            $targetBaseRate = $this->getBaseRate($targetCurrency);

            return $this->math->divide($this->math->multiply($currentAmount, $targetBaseRate), $currentBaseRate);
        } catch (Exception $e) {
            throw new CouldNotConvertConvertCurrencyException('Could not convert currency', 0, $e);
        }
    }

    /**
     * @param string $currency
     * @return string
     * @throws CurrencyConverterException
     */
    private function getBaseRate(string $currency): string
    {
        $rates = $this->getRates();
        $key = self::BASE_CURRENCY . $currency;

        if (!isset($rates[$key])) {
            throw new CurrencyConverterException(sprintf('The given currency "%s" is not found in rates.', $currency));
        }

        return (string) $rates[$key];
    }

    /**
     * @return array
     * @throws CurrencyConverterException
     */
    private function getRates(): array
    {
        try {
            if (empty($this->rates)) {
                $this->rates = $this->apiCurrencyLayer->getExchangeRateData();
            }

            return $this->rates;
        } catch (Exception $e) {
            throw new CurrencyConverterException('Could not request currency rates.', 0, $e);
        }
    }
}
