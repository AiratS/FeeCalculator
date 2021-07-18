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
    /**
     * @var int
     */
    private int $feeScale;

    /**
     * @var TransactionDataParsersContainer
     */
    private TransactionDataParsersContainer $parsersContainer;

    /**
     * @var FileFormatResolver
     */
    private FileFormatResolver $formatResolver;

    /**
     * @var FeeCalculatorsContainer
     */
    private FeeCalculatorsContainer $calculatorsContainer;

    /**
     * @param int $feeScale
     * @param TransactionDataParsersContainer $parsersContainer
     * @param FileFormatResolver $formatResolver
     * @param FeeCalculatorsContainer $calculatorsContainer
     */
    public function __construct(
        int $feeScale,
        TransactionDataParsersContainer $parsersContainer,
        FileFormatResolver $formatResolver,
        FeeCalculatorsContainer $calculatorsContainer
    ) {
        $this->feeScale = $feeScale;
        $this->parsersContainer = $parsersContainer;
        $this->formatResolver = $formatResolver;
        $this->calculatorsContainer = $calculatorsContainer;
    }

    /**
     * @param string $filePath
     * @return iterable
     * @throws UnsupportedFormatException
     * @throws FeeCalculatorIsNotFoundException
     */
    public function getCalculatedFees(string $filePath): iterable
    {
        $format = $this->formatResolver->resolveFormat($filePath);
        $parser = $this->parsersContainer->getTransactionDataParser($format);

        /** @var TransactionData $data */
        foreach ($parser->parse($filePath) as $data) {
            $calculator = $this->calculatorsContainer->getCalculator($data->getOperationType(), $data->getUserType());
            yield $calculator->getFee($data);
        }
    }
}
