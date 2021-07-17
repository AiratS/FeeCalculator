<?php

declare(strict_types=1);

namespace App\Enum;

abstract class AbstractEnum
{
    abstract static public function getItems(): array;
}
