<?php

declare(strict_types=1);

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\FeeCalculator\DepositFeeCalculator;
use App\FeeCalculator\FeeCalculatorsContainer;
use App\FeeCalculator\WithdrawBusinessClientFeeCalculator;
use App\FeeCalculator\WithdrawPrivateClientFeeCalculator;
use App\TransactionData\TransactionData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class FeeCalculatorTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private ContainerBuilder $container;

    /**
     * @var FeeCalculatorsContainer
     */
    private FeeCalculatorsContainer $calculatorsContainer;

    /**
     * @dataProvider transactionDataProvider
     * @throws Exception
     */
    public function testFeeCalculation(TransactionData $transaction, string $fee, string $className)
    {
        $calculators = $this->getCalculatorsContainer();
        $calculator = $calculators->getCalculator($transaction->getOperationType(), $transaction->getUserType());
        $this->assertEquals($calculator->getFee($transaction), $fee);
    }

    /**
     * @dataProvider transactionDataProvider
     * @param TransactionData $transaction
     * @param string $fee
     * @param string $className
     * @throws Exception
     */
    public function testFeeCalculatorService(TransactionData $transaction, string $fee, string $className)
    {
        $calculators = $this->getCalculatorsContainer();
        $calculator = $calculators->getCalculator($transaction->getOperationType(), $transaction->getUserType());
        $this->assertEquals(get_class($calculator), $className);
    }

    /**
     * @return array[]
     */
    public function transactionDataProvider(): array
    {
        $transaction1 = (new TransactionData())
            ->setDate(new DateTime('2014-12-31'))
            ->setUserId(4)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1200.00')
            ->setCurrency('EUR');

        $transaction2 = (new TransactionData())
            ->setDate(new DateTime('2015-01-01'))
            ->setUserId(4)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');

        $transaction3 = (new TransactionData())
            ->setDate(new DateTime('2016-01-05'))
            ->setUserId(4)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');

        $transaction4 = (new TransactionData())
            ->setDate(new DateTime('2016-01-05'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::DEPOSIT)
            ->setAmount('200.00')
            ->setCurrency('EUR');

        $transaction5 = (new TransactionData())
            ->setDate(new DateTime('2016-01-06'))
            ->setUserId(2)
            ->setUserType(TransactionUserType::BUSINESS)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('300.00')
            ->setCurrency('EUR');

        $transaction6 = (new TransactionData())
            ->setDate(new DateTime('2016-01-06'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('30000')
            ->setCurrency('JPY');

        $transaction7 = (new TransactionData())
            ->setDate(new DateTime('2016-01-07'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');

        $transaction8 = (new TransactionData())
            ->setDate(new DateTime('2016-01-07'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('100.00')
            ->setCurrency('USD');

        $transaction9 = (new TransactionData())
            ->setDate(new DateTime('2016-01-10'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('100.00')
            ->setCurrency('EUR');

        $transaction10 = (new TransactionData())
            ->setDate(new DateTime('2016-01-10'))
            ->setUserId(2)
            ->setUserType(TransactionUserType::BUSINESS)
            ->setOperationType(TransactionOperationType::DEPOSIT)
            ->setAmount('10000.00')
            ->setCurrency('EUR');

        $transaction11 = (new TransactionData())
            ->setDate(new DateTime('2016-01-10'))
            ->setUserId(3)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');

        $transaction12 = (new TransactionData())
            ->setDate(new DateTime('2016-02-15'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('300.00')
            ->setCurrency('EUR');

        $transaction13 = (new TransactionData())
            ->setDate(new DateTime('2016-02-19'))
            ->setUserId(5)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('3000000')
            ->setCurrency('JPY');

        return [
            [$transaction1, '0.60', WithdrawPrivateClientFeeCalculator::class],
            [$transaction2, '3.00', WithdrawPrivateClientFeeCalculator::class],
            [$transaction3, '0.00', WithdrawPrivateClientFeeCalculator::class],
            [$transaction4, '0.06', DepositFeeCalculator::class],
            [$transaction5, '1.50', WithdrawBusinessClientFeeCalculator::class],
            [$transaction6, '0', WithdrawPrivateClientFeeCalculator::class],
            [$transaction7, '0.70', WithdrawPrivateClientFeeCalculator::class],
            [$transaction8, '0.30', WithdrawPrivateClientFeeCalculator::class],
            [$transaction9, '0.30', WithdrawPrivateClientFeeCalculator::class],
            [$transaction10, '3.00', DepositFeeCalculator::class],
            [$transaction11, '0.00', WithdrawPrivateClientFeeCalculator::class],
            [$transaction12, '0.00', WithdrawPrivateClientFeeCalculator::class],
            [$transaction13, '8612', WithdrawPrivateClientFeeCalculator::class]
        ];
    }

    /**
     * @return FeeCalculatorsContainer
     * @throws Exception
     */
    private function getCalculatorsContainer(): FeeCalculatorsContainer
    {
        if (!isset($this->calculatorsContainer)) {
            $this->calculatorsContainer = new FeeCalculatorsContainer([
                $this->getContainerBuilder()->get('app.deposit_fee_calculator'),
                $this->getContainerBuilder()->get('app.withdraw_private_client_fee_calculator'),
                $this->getContainerBuilder()->get('app.withdraw_business_client_fee_calculator'),
            ]);
        }

        return $this->calculatorsContainer;
    }

    /**
     * @return ContainerBuilder
     * @throws Exception
     */
    private function getContainerBuilder(): ContainerBuilder
    {
        if (!isset($this->container)) {
            $this->container = new ContainerBuilder();
            $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
            $loader->load('../config/services.yaml');
        }

        return $this->container;
    }
}
