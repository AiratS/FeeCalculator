<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\Exception\CouldNotConvertCurrencyException;
use App\Service\CurrencyConverter;
use App\Service\Math;
use App\TransactionData\TransactionData;

class WithdrawPrivateClientFeeCalculator implements FeeCalculatorInterface
{
    private float $feePercentage;
    private float $feeMaxFreeAmount;
    private int $feeFreeOperationsCount;
    private string $defaultCurrency;
    private WithdrawPrivateClientWeekTransactionHistory $history;
    private CurrencyConverter $converter;
    private Math $math;

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
     */
    public function setWeekTransactionHistory(WithdrawPrivateClientWeekTransactionHistory $history)
    {
        $this->history = $history;
    }

    /**
     * @required
     */
    public function setCurrencyConverter(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @required
     */
    public function setMath(Math $math)
    {
        $this->math = $math;
    }

    public function supportsOperationType(string $operationType): bool
    {
        return TransactionOperationType::WITHDRAW === $operationType;
    }

    public function supportsUserType(string $userType): bool
    {
        return TransactionUserType::PRIVATE === $userType;
    }

    /**
     * @throws CouldNotConvertCurrencyException
     */
    public function getFee(TransactionData $transaction): string
    {
        $transactions = $this->history->getSameWeekTransactions($transaction);
        $defaultCurrencyAmount = $this->convertToDefaultCurrency($transaction->getCurrency(), $transaction->getAmount());
        $feeAmount = $this->math->max($this->math->substract($defaultCurrencyAmount, (string) $this->feeMaxFreeAmount), '0.0');

        if (!empty($transactions)) {
            $feeAmount = $this->getTransactionsFeeAmount($transactions, $defaultCurrencyAmount);
        }

        if ($this->defaultCurrency !== $transaction->getCurrency()) {
            $feeAmount = $this->converter->convert($this->defaultCurrency, $transaction->getCurrency(), $feeAmount);
        }

        $this->history->addTransaction($transaction);

        return $this->math->percentage($feeAmount, (string) $this->feePercentage);
    }

    /**
     * @throws CouldNotConvertCurrencyException
     */
    private function getTransactionsFeeAmount(array $transactions, string $defaultCurrencyAmount): string
    {
        $feeAmount = $defaultCurrencyAmount;

        if ($this->feeFreeOperationsCount > count($transactions)) {
            $previousTotal = $this->getTransactionsTotalAmount($transactions);

            if ($this->math->moreThan((string) $this->feeMaxFreeAmount, $previousTotal)) {
                $total = $this->math->add($previousTotal, $defaultCurrencyAmount);
                $feeAmount = $this->math->max(
                    $this->math->substract($total, (string) $this->feeMaxFreeAmount),
                    '0.0'
                );
            }
        }

        return $feeAmount;
    }

    /**
     * @throws CouldNotConvertCurrencyException
     */
    private function getTransactionsTotalAmount(array $transactions): string
    {
        return array_reduce($transactions, function (string $total, TransactionData $transaction) {
            $defaultCurrencyAmount = $this->convertToDefaultCurrency($transaction->getCurrency(), $transaction->getAmount());
            return $this->math->add($total, $defaultCurrencyAmount);
        }, '0.0');
    }

    /**
     * @throws CouldNotConvertCurrencyException
     */
    private function convertToDefaultCurrency(string $currency, string $amount): string
    {
        if ($this->defaultCurrency !== $currency) {
            return $this->converter->convert($currency, $this->defaultCurrency, $amount);
        }

        return $amount;
    }
}
