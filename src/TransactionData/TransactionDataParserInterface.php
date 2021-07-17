<?php

declare(strict_types=1);

namespace App\TransactionData;

interface TransactionDataParserInterface
{
    /**
     * @param string $format
     * @return bool
     */
    public function supportsFormat(string $format): bool;

    /**
     * @param string $filePath
     * @return iterable
     */
    public function parse(string $filePath): iterable;
}
