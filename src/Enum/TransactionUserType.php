<?php

declare(strict_types=1);

namespace App\Enum;

abstract class TransactionUserType extends AbstractEnum
{
    public const PRIVATE = 'private';
    public const BUSINESS = 'business';

    public static function getItems(): array
    {
        return [
            self::PRIVATE,
            self::BUSINESS,
        ];
    }
}
