<?php
declare(strict_types=1);

namespace App\Controller;

use App\Util\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommissionController extends AbstractController
{
    public function moneyDeposit(float $amount): float
    {
        $commissionFee = (($amount * Parameter::get('pricing')['MONEY_DEPOSIT_PERCENT']) / 100);

        if ($commissionFee >= Parameter::get('pricing')['MONEY_DEPOSIT_MAXIMUM_COMMISSION']) {
            $commissionFee = Parameter::get('pricing')['MONEY_DEPOSIT_MAXIMUM_COMMISSION'];
        }

        return $commissionFee;
    }

    public function cashClearingForLegalPeople(float $amount): float
    {
        $commissionFee = ($amount * Parameter::get('pricing')['CASH_CLEARING_PERCENT']) / 100;

        if ($commissionFee <= Parameter::get('pricing')['CASH_CLEARING_MINIMUM_COMMISSION']) {
            $commissionFee = Parameter::get('pricing')['CASH_CLEARING_MINIMUM_COMMISSION'];
        }

        return $commissionFee;
    }

    public function cashClearingForNaturalPeople(
        float $amount,
        int $time,
        float $previousOperationSum
    ): float {

        if ($time > Parameter::get('pricing')['CASH_CLEARING_AMOUNT_OF_TIMES_FREE_FOR_NATURAL_PEOPLE']) {
            $commissionFee = ($amount * Parameter::get('pricing')['CASH_CLEARING_PERCENT']) / 100;
        } else {
            if ($previousOperationSum > Parameter::get('pricing')['CASH_CLEARING_FREE_AMOUNT']) {
                $commissionFee = ($amount * Parameter::get('pricing')['CASH_CLEARING_PERCENT']) / 100;
            } elseif ($previousOperationSum < Parameter::get('pricing')['CASH_CLEARING_FREE_AMOUNT'] &&
                ($amount + $previousOperationSum) < Parameter::get('pricing')['CASH_CLEARING_FREE_AMOUNT']) {
                $commissionFee = 0;
            } else {
                $commissionFee =
                    (($amount - (Parameter::get('pricing')['CASH_CLEARING_FREE_AMOUNT'] - $previousOperationSum))
                    * Parameter::get('pricing')['CASH_CLEARING_PERCENT']) / 100;
            }
        }

        return $commissionFee;
    }
}
