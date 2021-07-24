<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\Service\Math;
use App\TransactionData\TransactionData;

class DepositFeeCalculator implements FeeCalculatorInterface
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
        return TransactionOperationType::DEPOSIT === $operationType;
    }

    public function supportsUserType(string $userType): bool
    {
        return in_array($userType, TransactionUserType::getItems());
    }

    public function getFee(TransactionData $transaction): string
    {
        return $this->math->percentage($transaction->getAmount(), (string) $this->feePercentage);
    }
}
