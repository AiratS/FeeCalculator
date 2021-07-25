<?php

declare(strict_types=1);

namespace App\TransactionData;

use App\Enum\CsvTransactionDataColumn;
use DateTime;
use Exception;
use App\Exception\CouldNotOpenFileException;

class CsvTransactionDataParser implements TransactionDataParserInterface
{
    public const FORMAT_CSV = 'csv';

    private CsvTransactionDataValidator $validator;

    public function __construct(CsvTransactionDataValidator $validator)
    {
        $this->validator = $validator;
    }

    public function supportsFormat(string $format): bool
    {
        return self::FORMAT_CSV === $format;
    }

    /**
     * @throws Exception
     * @throws CouldNotOpenFileException
     */
    public function parse(string $filePath): iterable
    {
        $resource = fopen($filePath, 'r');
        if (!$resource) {
            throw new CouldNotOpenFileException();
        }

        while (($rowData = fgetcsv($resource))) {
            $this->validator->validate($rowData);

            yield (new TransactionData())
                ->setDate(new DateTime($rowData[CsvTransactionDataColumn::DATE]))
                ->setUserId((int) $rowData[CsvTransactionDataColumn::USER_ID])
                ->setUserType($rowData[CsvTransactionDataColumn::USER_TYPE])
                ->setOperationType($rowData[CsvTransactionDataColumn::OPERATION_TYPE])
                ->setAmount($rowData[CsvTransactionDataColumn::AMOUNT])
                ->setCurrency($rowData[CsvTransactionDataColumn::CURRENCY])
            ;
        }

        fclose($resource);
    }
}
