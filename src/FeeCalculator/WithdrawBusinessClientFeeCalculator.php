<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\Service\Math;
use App\TransactionData\TransactionData;

class WithdrawBusinessClientFeeCalculator implements FeeCalculatorInterface
{
    private float $feePercentage;
    private Math $math;

    public function __construct(float $feePercentage, Math $math)
    {
        $this->feePercentage = $feePercentage;
        $this->math = $math;
    }

    public function supportsOperationType(string $operationType): bool
    {
        return TransactionOperationType::WITHDRAW === $operationType;
    }

    public function supportsUserType(string $userType): bool
    {
        return TransactionUserType::BUSINESS === $userType;
    }

    public function getFee(TransactionData $transaction): string
    {
        return $this->math->percentage($transaction->getAmount(), (string) $this->feePercentage);
    }
}
