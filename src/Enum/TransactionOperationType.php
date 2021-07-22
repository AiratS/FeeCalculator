<?php

declare(strict_types=1);

namespace App\Enum;

abstract class TransactionOperationType extends AbstractEnum
{
    public const DEPOSIT = 'deposit';
    public const WITHDRAW = 'withdraw';

    public static function getItems(): array
    {
        return [
            self::DEPOSIT,
            self::WITHDRAW,
        ];
    }
}
