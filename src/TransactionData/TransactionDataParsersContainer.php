<?php

declare(strict_types=1);

namespace App\TransactionData;

use App\Exception\UnsupportedFormatException;

class TransactionDataParsersContainer
{
    /**
     * @var iterable
     */
    private iterable $transactionDataParsers;

    /**
     * @param iterable $transactionDataParsers
     */
    public function __construct(iterable $transactionDataParsers)
    {
        $this->transactionDataParsers = $transactionDataParsers;
    }

    /**
     * @param string $format
     * @return TransactionDataParserInterface
     * @throws UnsupportedFormatException
     */
    public function getTransactionDataParser(string $format): TransactionDataParserInterface
    {
        foreach ($this->transactionDataParsers as $transactionDataParser) {
            if ($transactionDataParser->supportsFormat($format)) {
                return $transactionDataParser;
            }
        }

        throw new UnsupportedFormatException(sprintf('Parser for given format "%s" is not found.', $format));
    }
}
