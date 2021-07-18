<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\Exception\CouldNotConvertConvertCurrencyException;
use App\Service\CurrencyConverter;
use App\Service\Math;
use App\TransactionData\TransactionData;

class WithdrawPrivateClientFeeCalculator implements FeeCalculatorInterface
{
    /**
     * @var float
     */
    private float $feePercentage;

    /**
     * @var float
     */
    private float $feeMaxFreeAmount;

    /**
     * @var int
     */
    private int $feeFreeOperationsCount;

    /**
     * @var string
     */
    private string $defaultCurrency;

    /**
     * @var WithdrawPrivateClientWeekTransactionHistory
     */
    private WithdrawPrivateClientWeekTransactionHistory $history;

    /**
     * @var CurrencyConverter
     */
    private CurrencyConverter $converter;

    /**
     * @var Math
     */
    private Math $math;

    /**
     * @param float $feePercentage
     * @param float $feeMaxFreeAmount
     * @param int $feeFreeOperationsCount
     * @param string $defaultCurrency
     */
    public function __construct(
        float $feePercentage,
        float $feeMaxFreeAmount,
        int $feeFreeOperationsCount,
        string $defaultCurrency
    ) {
        $this->feePercentage = $feePercentage;
        $this->feeMaxFreeAmount = $feeMaxFreeAmount;
        $this->feeFreeOperationsCount = $feeFreeOperationsCount;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @required
     * @param WithdrawPrivateClientWeekTransactionHistory $history
     */
    public function setWeekTransactionHistory(WithdrawPrivateClientWeekTransactionHistory $history)
    {
        $this->history = $history;
    }

    /**
     * @required
     * @param CurrencyConverter $converter
     */
    public function setCurrencyConverter(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @required
     * @param Math $math
     */
    public function setMath(Math $math)
    {
        $this->math = $math;
    }

    /**
     * @param string $operationType
     * @return bool
     */
    public function supportsOperationType(string $operationType): bool
    {
        return TransactionOperationType::WITHDRAW === $operationType;
    }

    /**
     * @param string $userType
     * @return bool
     */
    public function supportsUserType(string $userType): bool
    {
        return TransactionUserType::PRIVATE === $userType;
    }

    /**
     * @param TransactionData $transactionData
     * @return string
     * @throws CouldNotConvertConvertCurrencyException
     */
    public function getFee(TransactionData $transactionData): string
    {
        $transactions = $this->history->getSameWeekTransactions($transactionData);
        $defaultCurrencyAmount = $this->convertToDefaultCurrency(
            $transactionData->getCurrency(),
            $transactionData->getAmount()
        );
        $needFeeAmount = $defaultCurrencyAmount;

        if (!empty($transactions)) {
            if ($this->feeFreeOperationsCount > count($transactions)) {
                $previousTotal = $this->getTransactionsTotalAmount($transactions);

                if ($this->math->moreThan((string) $this->feeMaxFreeAmount, $previousTotal)) {
                    $total = $this->math->add($previousTotal, $defaultCurrencyAmount);
                    $needFeeAmount = $this->math->max(
                        $this->math->substract($total, (string) $this->feeMaxFreeAmount),
                        '0.0'
                    );
                }
            }
        } else {
            $needFeeAmount = $this->math->max(
                $this->math->substract($defaultCurrencyAmount, (string) $this->feeMaxFreeAmount),
                '0.0'
            );
        }

        if ($this->defaultCurrency !== $transactionData->getCurrency()) {
            $needFeeAmount = $this->converter->convert(
                $this->defaultCurrency,
                $transactionData->getCurrency(),
                $needFeeAmount
            );
        }

        $this->history->addTransaction($transactionData);

        return $this->math->percentage($needFeeAmount, (string) $this->feePercentage);
    }

    /**
     * @param array $transactions
     * @return string
     * @throws CouldNotConvertConvertCurrencyException
     */
    private function getTransactionsTotalAmount(array $transactions): string
    {
        return array_reduce($transactions, function (string $total, TransactionData $transactionData) {
            $defaultCurrencyAmount = $this->convertToDefaultCurrency($transactionData->getCurrency(), $transactionData->getAmount());
            return $this->math->add($total, $defaultCurrencyAmount);
        }, '0.0');
    }

    /**
     * @param string $currency
     * @param string $amount
     * @return string
     * @throws CouldNotConvertConvertCurrencyException
     */
    private function convertToDefaultCurrency(string $currency, string $amount): string
    {
        if ($this->defaultCurrency !== $currency) {
            return $this->converter->convert($currency, $this->defaultCurrency, $amount);
        }

        return $amount;
    }
}
