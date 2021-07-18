<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\TransactionData\TransactionData;

interface FeeCalculatorInterface
{
    /**
     * @param string $operationType
     * @return bool
     */
    public function supportsOperationType(string $operationType): bool;

    /**
     * @param string $userType
     * @return bool
     */
    public function supportsUserType(string $userType): bool;

    /**
     * @param TransactionData $transactionData
     * @return string
     */
    public function getFee(TransactionData $transactionData): string;
}
