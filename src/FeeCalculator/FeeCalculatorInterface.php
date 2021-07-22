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
     * @param TransactionData $transaction
     * @return string
     */
    public function getFee(TransactionData $transaction): string;
}
