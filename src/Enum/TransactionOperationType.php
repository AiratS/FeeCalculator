<?php

declare(strict_types=1);

namespace App\Enum;

abstract class TransactionOperationType extends AbstractEnum
{
    const DEPOSIT = 'deposit';
    const WITHDRAW = 'withdraw';

    /**
     * @return array
     */
    public static function getItems(): array
    {
        return [
            self::DEPOSIT,
            self::WITHDRAW,
        ];
    }
}
