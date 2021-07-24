<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\FeeCalculatorIsNotFoundException;
use App\Exception\UnsupportedFormatException;
use App\FeeCalculator\FeeCalculatorsContainer;
use App\TransactionData\TransactionData;
use App\TransactionData\TransactionDataParsersContainer;

class FileTransactionFeeCalculator
{
    private TransactionDataParsersContainer $parsersContainer;
    private FileFormatResolver $formatResolver;
    private FeeCalculatorsContainer $calculatorsContainer;
    private CurrencyRounder $currencyRounder;

    public function __construct(
        TransactionDataParsersContainer $parsersContainer,
        FileFormatResolver $formatResolver,
        FeeCalculatorsContainer $calculatorsContainer,
        CurrencyRounder $currencyRounder
    ) {
        $this->parsersContainer = $parsersContainer;
        $this->formatResolver = $formatResolver;
        $this->calculatorsContainer = $calculatorsContainer;
        $this->currencyRounder = $currencyRounder;
    }

    /**
     * @throws UnsupportedFormatException
     * @throws FeeCalculatorIsNotFoundException
     */
    public function getCalculatedFees(string $filePath): iterable
    {
        $format = $this->formatResolver->resolveFormat($filePath);
        $parser = $this->parsersContainer->getTransactionDataParser($format);

        /** @var TransactionData $transaction */
        foreach ($parser->parse($filePath) as $transaction) {
            $calculator = $this->calculatorsContainer->getCalculator($transaction->getOperationType(), $transaction->getUserType());
            yield $this->currencyRounder->round($calculator->getFee($transaction), $transaction->getCurrency());
        }
    }
}
