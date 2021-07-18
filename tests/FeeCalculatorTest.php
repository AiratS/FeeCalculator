<?php

declare(strict_types=1);

use App\Enum\TransactionOperationType;
use App\Enum\TransactionUserType;
use App\FeeCalculator\FeeCalculatorsContainer;
use App\TransactionData\TransactionData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class FeeCalculatorTest extends TestCase
{
    /**
     * @var FeeCalculatorsContainer
     */
    private FeeCalculatorsContainer $calculatorsContainer;

    /**
     * @throws Exception
     */
    public function testFeeCalculation()
    {
        $transactions = $this->getFeeCalculationTestData();

        foreach ($transactions as $data) {
            $transaction = $data['transaction'];
            $calculators = $this->getCalculatorsContainer();
            $calculator = $calculators->getCalculator($transaction->getOperationType(), $transaction->getUserType());
            $this->assertEquals($data['fee'], $calculator->getFee($transaction));
        }
    }

    /**
     * @return iterable
     */
    public function getFeeCalculationTestData(): iterable
    {
        $transaction1 = (new TransactionData())
            ->setDate(new DateTime('2014-12-31'))
            ->setUserId(4)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1200.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction1, 'fee' => '0.60'];

        $transaction2 = (new TransactionData())
            ->setDate(new DateTime('2015-01-01'))
            ->setUserId(4)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction2, 'fee' => '3.00'];

        $transaction3 = (new TransactionData())
            ->setDate(new DateTime('2016-01-05'))
            ->setUserId(4)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction3, 'fee' => '0.00'];

        $transaction4 = (new TransactionData())
            ->setDate(new DateTime('2016-01-05'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::DEPOSIT)
            ->setAmount('200.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction4, 'fee' => '0.06'];

        $transaction5 = (new TransactionData())
            ->setDate(new DateTime('2016-01-06'))
            ->setUserId(2)
            ->setUserType(TransactionUserType::BUSINESS)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('300.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction5, 'fee' => '1.50'];

        $transaction6 = (new TransactionData())
            ->setDate(new DateTime('2016-01-06'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('30000')
            ->setCurrency('JPY');
        yield ['transaction' => $transaction6, 'fee' => '0'];

        $transaction7 = (new TransactionData())
            ->setDate(new DateTime('2016-01-07'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction7, 'fee' => '0.70'];

        $transaction8 = (new TransactionData())
            ->setDate(new DateTime('2016-01-07'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('100.00')
            ->setCurrency('USD');
        yield ['transaction' => $transaction8, 'fee' => '0.30'];

        $transaction9 = (new TransactionData())
            ->setDate(new DateTime('2016-01-10'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('100.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction9, 'fee' => '0.30'];

        $transaction10 = (new TransactionData())
            ->setDate(new DateTime('2016-01-10'))
            ->setUserId(2)
            ->setUserType(TransactionUserType::BUSINESS)
            ->setOperationType(TransactionOperationType::DEPOSIT)
            ->setAmount('10000.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction10, 'fee' => '3.00'];

        $transaction11 = (new TransactionData())
            ->setDate(new DateTime('2016-01-10'))
            ->setUserId(3)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('1000.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction11, 'fee' => '0.00'];

        $transaction12 = (new TransactionData())
            ->setDate(new DateTime('2016-02-15'))
            ->setUserId(1)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('300.00')
            ->setCurrency('EUR');
        yield ['transaction' => $transaction12, 'fee' => '0.00'];

        $transaction13 = (new TransactionData())
            ->setDate(new DateTime('2016-02-19'))
            ->setUserId(5)
            ->setUserType(TransactionUserType::PRIVATE)
            ->setOperationType(TransactionOperationType::WITHDRAW)
            ->setAmount('3000000')
            ->setCurrency('JPY');
        yield ['transaction' => $transaction13, 'fee' => '8612'];
    }

    /**
     * @return FeeCalculatorsContainer
     * @throws Exception
     */
    private function getCalculatorsContainer(): FeeCalculatorsContainer
    {
        if (!isset($this->calculatorsContainer)) {
            $container = new ContainerBuilder();
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
            $loader->load('../config/services.yaml');

            $this->calculatorsContainer = new FeeCalculatorsContainer([
                $container->get('app.deposit_fee_calculator'),
                $container->get('app.withdraw_private_client_fee_calculator'),
                $container->get('app.withdraw_business_client_fee_calculator'),
            ]);
        }

        return $this->calculatorsContainer;
    }
}
