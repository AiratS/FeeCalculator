<?php

declare(strict_types=1);

namespace App\TransactionData;

use App\Enum\CsvTransactionDataColumn;
use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\Exception\InvalidParameterException;
use App\Exception\NotEnoughParametersException;
use App\Service\SupportedCurrency;

class CsvTransactionDataValidator
{
    /**
     * @var SupportedCurrency
     */
    private SupportedCurrency $supportedCurrency;

    /**
     * @param SupportedCurrency $supportedCurrency
     */
    public function __construct(SupportedCurrency $supportedCurrency)
    {
        $this->supportedCurrency = $supportedCurrency;
    }

    /**
     * @param array $rowData
     * @throws InvalidParameterException
     * @throws NotEnoughParametersException
     */
    public function validate(array $rowData)
    {
        if (count(CsvTransactionDataColumn::getItems()) !== count($rowData)) {
            throw new NotEnoughParametersException();
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $rowData[CsvTransactionDataColumn::DATE])) {
            throw new InvalidParameterException('The given data is invalid.');
        }

        if (!preg_match('/^\d+$/', $rowData[CsvTransactionDataColumn::USER_ID])) {
            throw new InvalidParameterException('The given user id is invalid.');
        }

        if (!in_array($rowData[CsvTransactionDataColumn::USER_TYPE], TransactionUserType::getItems())) {
            throw new InvalidParameterException('The given user type is invalid.');
        }

        if (!in_array($rowData[CsvTransactionDataColumn::OPERATION_TYPE], TransactionOperationType::getItems())) {
            throw new InvalidParameterException('The given operation type is invalid.');
        }

        if (!preg_match('/^\d+(?:\.\d+)?$/', $rowData[CsvTransactionDataColumn::AMOUNT])) {
            throw new InvalidParameterException('The given money amount is invalid.');
        }

        if (!$this->supportedCurrency->isSupported($rowData[CsvTransactionDataColumn::CURRENCY])) {
            throw new InvalidParameterException('The given currency is not supported by application.');
        }
    }
}
