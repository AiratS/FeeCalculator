<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use App\Exception\FeeCalculatorIsNotFoundException;

class FeeCalculatorsContainer
{
    /**
     * @var iterable
     */
    public iterable $calculators;

    /**
     * @var array
     */
    private array $cachedCalculators = [];

    /**
     * @param iterable $calculators
     */
    public function __construct(iterable $calculators = [])
    {
        $this->calculators = $calculators;
    }

    /**
     * @param string $operationType
     * @param string $userType
     * @return FeeCalculatorInterface
     * @throws FeeCalculatorIsNotFoundException
     */
    public function getCalculator(string $operationType, string $userType): FeeCalculatorInterface
    {
        $cachedCalculator = $this->cachedCalculators[$operationType][$userType];
        if (isset($this->cachedCalculators[$operationType][$userType])) {
            return $cachedCalculator;
        }

        foreach ($this->calculators as $calculator) {
            if ($calculator->supportsOperationType($operationType) && $calculator->supportsUserType($userType)) {
                $this->cachedCalculators[$operationType][$userType] = $calculator;

                return $calculator;
            }
        }

        throw new FeeCalculatorIsNotFoundException('Could not find any calculator services for given parameters.');
    }
}
