<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\TransactionData\TransactionData;

interface FeeCalculatorInterface
{
    public function supportsOperationType(string $operationType): bool;

    public function supportsUserType(string $userType): bool;

    public function getFee(TransactionData $transaction): string;
}
