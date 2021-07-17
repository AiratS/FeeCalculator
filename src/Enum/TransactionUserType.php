<?php

declare(strict_types=1);

namespace App\Enum;

abstract class TransactionUserType extends AbstractEnum
{
    const PRIVATE = 'private';
    const BUSINESS = 'business';

    /**
     * @return array
     */
    public static function getItems(): array
    {
        return [
            self::PRIVATE,
            self::BUSINESS,
        ];
    }
}
