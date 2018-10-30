<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Dotenv\Dotenv;

class CommissionManager
{
    private $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load('../BankCustomerServiceCenter/config/parameters.env');
    }

    public function moneyDeposit(float $amount): float
    {
        $commissionFee = (($amount * getenv('MONEY_DEPOSIT_PERCENT')) / 100);

        if ($commissionFee >= getenv('MONEY_DEPOSIT_MAXIMUM_COMMISSION')) {
            return floatval(getenv('MONEY_DEPOSIT_MAXIMUM_COMMISSION'));
        }

        return $commissionFee;
    }

    public function cashClearingForLegalPeople(float $amount): float
    {
        $commissionFee = ($amount * getenv('CASH_CLEARING_PERCENT')) / 100;

        if ($commissionFee <= getenv('CASH_CLEARING_MINIMUM_COMMISSION')) {
            return floatval(getenv('CASH_CLEARING_MINIMUM_COMMISSION'));
        }

        return $commissionFee;
    }

    public function cashClearingForNaturalPeople(
        float $amount,
        int $time,
        float $previousOperationSum
    ): float {

        if ($time > getenv('CASH_CLEARING_AMOUNT_OF_TIMES_FREE_FOR_NATURAL_PEOPLE')) {
            $commissionFee = ($amount * getenv('CASH_CLEARING_PERCENT')) / 100;
        } else {
            if ($previousOperationSum > getenv('CASH_CLEARING_FREE_AMOUNT')) {
                $commissionFee = ($amount * getenv('CASH_CLEARING_PERCENT')) / 100;
            } elseif ($previousOperationSum < getenv('CASH_CLEARING_FREE_AMOUNT') &&
                ($amount + $previousOperationSum) < getenv('CASH_CLEARING_FREE_AMOUNT')) {
                $commissionFee = 0;
            } else {
                $commissionFee =
                    (($amount - (getenv('CASH_CLEARING_FREE_AMOUNT') - $previousOperationSum))
                        * getenv('CASH_CLEARING_PERCENT')) / 100;
            }
        }

        return $commissionFee;
    }
}
