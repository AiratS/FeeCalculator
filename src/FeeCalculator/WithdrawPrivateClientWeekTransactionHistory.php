<?php

declare(strict_types=1);

namespace App\FeeCalculator;

use DateTime;
use App\TransactionData\TransactionData;

class WithdrawPrivateClientWeekTransactionHistory
{
    /**
     * @var array
     */
    public array $transactions = [];

    /**
     * @param TransactionData $transaction
     * @return array
     */
    public function getSameWeekTransactions(TransactionData $transaction): array
    {
        return $this->hasSameWeekTransactions($transaction)
            ? $this->transactions[$transaction->getUserId()]
            : [];
    }

    /**
     * @param TransactionData $transaction
     */
    public function addTransaction(TransactionData $transaction)
    {
        $userId = $transaction->getUserId();

        if (isset($this->transactions[$userId]) && !$this->hasSameWeekTransactions($transaction)) {
            /**
             * In order not to store in memory in previous weeks, since we
             * no longer need them, we delete them (transactions are sorted by date)
             */
            unset($this->transactions[$userId]);
        }

        $this->transactions[$userId][] = $transaction;
    }

    /**
     * @param TransactionData $transaction
     * @return bool
     */
    public function hasSameWeekTransactions(TransactionData $transaction): bool
    {
        $userId = $transaction->getUserId();
        if (!isset($this->transactions[$userId])) {
            return false;
        }

        $lastIdx = count($this->transactions[$userId]) - 1;
        $lastTransaction = $this->transactions[$userId][$lastIdx];

        return $this->isSameWeek($transaction->getDate(), $lastTransaction->getDate());
    }

    /**
     * @param DateTime $dateTime1
     * @param DateTime $dateTime2
     * @return bool
     */
    private function isSameWeek(DateTime $dateTime1, DateTime $dateTime2): bool
    {
        $weekStart = clone $dateTime1;
        $weekStart->modify('this week');

        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 day');

        return $weekStart <= $dateTime2 && $weekEnd >= $dateTime2;
    }
}
