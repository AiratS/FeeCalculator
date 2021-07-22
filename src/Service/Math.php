<?php

declare(strict_types=1);

namespace App\Service;

class Math
{
    /**
     * @param int $scale
     */
    public function __construct(int $scale = 0)
    {
        $this->setScale($scale);
    }

    /**
     * @param int $scale
     */
    public function setScale(int $scale)
    {
        bcscale($scale);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return string
     */
    public function add(string $num1, string $num2): string
    {
        return bcadd($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return string
     */
    public function substract(string $num1, string $num2): string
    {
        return bcsub($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return string
     */
    public function multiply(string $num1, string $num2): string
    {
        return bcmul($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return string
     */
    public function divide(string $num1, string $num2): string
    {
        return bcdiv($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return int
     */
    public function compare(string $num1, string $num2): int
    {
        return bccomp($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return bool
     */
    public function equals(string $num1, string $num2): bool
    {
        return 0 === $this->compare($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return bool
     */
    public function lessThan(string $num1, string $num2): bool
    {
        return -1 === $this->compare($num1, $num2);
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return bool
     */
    public function moreThan(string $num1, string $num2): bool
    {
        return 1 === $this->compare($num1, $num2);
    }

    /**
     * @param string $total
     * @param string $percentage
     * @return string
     */
    public function percentage(string $total, string $percentage): string
    {
        return $this->multiply($total, $this->divide($percentage, '100'));
    }

    /**
     * @param string $num1
     * @param string $num2
     * @return string
     */
    public function max(string $num1, string $num2): string
    {
        return $this->moreThan($num1, $num2) ? $num1 : $num2;
    }
}
