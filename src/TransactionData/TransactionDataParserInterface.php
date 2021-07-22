<?php

declare(strict_types=1);

namespace App\TransactionData;

interface TransactionDataParserInterface
{
    public function supportsFormat(string $format): bool;

    public function parse(string $filePath): iterable;
}
