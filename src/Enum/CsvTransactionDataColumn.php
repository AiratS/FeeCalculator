<?php

declare(strict_types=1);

namespace App\Enum;

abstract class CsvTransactionDataColumn extends AbstractEnum
{
    public const DATE = 0;
    public const USER_ID = 1;
    public const USER_TYPE = 2;
    public const OPERATION_TYPE = 3;
    public const AMOUNT = 4;
    public const CURRENCY = 5;

    public static function getItems(): array
    {
        return [
            self::DATE,
            self::USER_ID,
            self::USER_TYPE,
            self::OPERATION_TYPE,
            self::AMOUNT,
            self::CURRENCY,
        ];
    }
}
