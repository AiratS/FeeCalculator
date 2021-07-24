<?php

use App\FeeCalculator\FeeCalculatorsContainer;

abstract class AbstractFeeCalculatorsTest extends AbstractAppTest
{
    /**
     * @throws Exception
     */
    protected function getFeeCalculatorsContainer(): FeeCalculatorsContainer
    {
        $container = self::getContainer();

        return new FeeCalculatorsContainer([
            $container->get('app.deposit_fee_calculator'),
            $container->get('app.withdraw_private_client_fee_calculator'),
            $container->get('app.withdraw_business_client_fee_calculator'),
        ]);
    }
}
