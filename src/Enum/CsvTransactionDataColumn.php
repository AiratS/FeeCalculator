<?php

declare(strict_types=1);

namespace App\Enum;

abstract class CsvTransactionDataColumn extends AbstractEnum
{
    const DATE = 0;
    const USER_ID = 1;
    const USER_TYPE = 2;
    const OPERATION_TYPE = 3;
    const AMOUNT = 4;
    const CURRENCY = 5;

    /**
     * @return array
     */
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
