<?php

declare(strict_types=1);

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\Exception\FeeCalculatorIsNotFoundException;

class FeeCalculatorsContainerInvalidTest extends AbstractFeeCalculatorsTest
{
    private const TRANSACTION_OPERATION_TYPE_INVALID = 'transaction_operation_type_invalid';
    private const TRANSACTION_USER_TYPE_INVALID = 'transaction_user_type_invalid';

    /**
     * @dataProvider feeCalculatorsContainerInvalidData
     * @throws Exception
     */
    public function testFeeCalculatorsContainer(string $operationType, string $userType)
    {
        $this->expectException(FeeCalculatorIsNotFoundException::class);

        $calculatorsContainer = $this->getFeeCalculatorsContainer();
        $calculatorsContainer->getCalculator($operationType, $userType);
    }

    public function feeCalculatorsContainerInvalidData(): iterable
    {
        // Some field is invalid
        yield [
            'operationType' => TransactionOperationType::DEPOSIT,
            'userType' => self::TRANSACTION_USER_TYPE_INVALID,
        ];

        yield [
            'operationType' => TransactionOperationType::WITHDRAW,
            'userType' => self::TRANSACTION_USER_TYPE_INVALID,
        ];

        yield [
            'operationType' => self::TRANSACTION_OPERATION_TYPE_INVALID,
            'userType' => TransactionUserType::PRIVATE,
        ];

        yield [
            'operationType' => self::TRANSACTION_OPERATION_TYPE_INVALID,
            'userType' => TransactionUserType::BUSINESS,
        ];

        // All fields are invalid
        yield [
            'operationType' => self::TRANSACTION_OPERATION_TYPE_INVALID,
            'userType' => self::TRANSACTION_OPERATION_TYPE_INVALID,
        ];
    }
}
