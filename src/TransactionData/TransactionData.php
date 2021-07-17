<?php

declare(strict_types=1);

namespace App\TransactionData;

use DateTime;

class TransactionData
{
    /**
     * @var DateTime
     */
    private DateTime $date;

    /**
     * @var int
     */
    private int $userId;

    /**
     * @var string
     */
    private string $userType;

    /**
     * @var string
     */
    private string $operationType;

    /**
     * @var string
     */
    private string $amount;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @param string $userType
     * @return $this
     */
    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @param string $operationType
     * @return $this
     */
    public function setOperationType(string $operationType): self
    {
        $this->operationType = $operationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
