<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\Enum\TransactionOperationType;
use App\Service\Math;
use App\TransactionData\TransactionData;

class DepositFeeCalculator implements FeeCalculatorInterface
{
    /**
     * @var float
     */
    private float $feePercentage;

    /**
     * @var Math
     */
    private Math $math;

    /**
     * @param float $feePercentage
     * @param Math $math
     */
    public function __construct(float $feePercentage, Math $math)
    {
        $this->feePercentage = $feePercentage;
        $this->math = $math;
    }

    /**
     * @param string $operationType
     * @return bool
     */
    public function supportsOperationType(string $operationType): bool
    {
        return TransactionOperationType::DEPOSIT === $operationType;
    }

    /**
     * @param string $userType
     * @return bool
     */
    public function supportsUserType(string $userType): bool
    {
        return true;
    }

    /**
     * @param TransactionData $transaction
     * @return string
     */
    public function getFee(TransactionData $transaction): string
    {
        return $this->math->percentage($transaction->getAmount(), (string) $this->feePercentage);
    }
}
