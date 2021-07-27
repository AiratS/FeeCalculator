<?php

declare(strict_types=1);

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\FeeCalculator\DepositFeeCalculator;
use App\FeeCalculator\WithdrawBusinessClientFeeCalculator;
use App\FeeCalculator\WithdrawPrivateClientFeeCalculator;

class FeeCalculatorsContainerValidTest extends AbstractFeeCalculatorsTest
{
    /**
     * @dataProvider feeCalculatorsContainerData
     * @throws Exception
     */
    public function testFeeCalculatorsContainer(string $operationType, string $userType, string $calculatorClass)
    {
        $calculatorsContainer = $this->getFeeCalculatorsContainer();
        $calculator = $calculatorsContainer->getCalculator($operationType, $userType);

        $this->assertEquals($calculatorClass, get_class($calculator));
    }

    public function feeCalculatorsContainerData(): array
    {
        return [
            [
                'operationType' => TransactionOperationType::DEPOSIT,
                'userType' => TransactionUserType::PRIVATE,
                'calculatorClass' => DepositFeeCalculator::class,
            ],
            [
                'operationType' => TransactionOperationType::DEPOSIT,
                'userType' => TransactionUserType::BUSINESS,
                'calculatorClass' => DepositFeeCalculator::class,
            ],
            [
                'operationType' => TransactionOperationType::WITHDRAW,
                'userType' => TransactionUserType::PRIVATE,
                'calculatorClass' => WithdrawPrivateClientFeeCalculator::class,
            ],
            [
                'operationType' => TransactionOperationType::WITHDRAW,
                'userType' => TransactionUserType::BUSINESS,
                'calculatorClass' => WithdrawBusinessClientFeeCalculator::class,
            ],
        ];
    }
}
