<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use Evp\Component\Money\Money;

class CommissionManager
{
    public function calculateCommission(
        array $arrayOfOperationObjects,
        array $discountInformation
    ): array {
        $counter = 0;

        foreach ($arrayOfOperationObjects as $operation) {
            if ($operation->getOperationType() === getenv('MONEY_DEPOSIT')) {
                $commission = $this->cashIn($operation->getMoney());
            } elseif ($operation->getUserType() === getenv('LEGAL_PEOPLE')) {
                $commission = $this->cashOutForLegalPeople($operation->getMoney());
            } else {
                $commission = $this->cashOutForNaturalPeople($operation->getMoney(), $discountInformation[$counter]);
            }

            $calculatedCommissions[$counter] =
                (new Money())
                    ->setAmount($commission)
                    ->setCurrency(getenv('MAIN_CURRENCY'))
            ;

            $counter++;
        }

        return $calculatedCommissions;
    }

    protected function cashIn(Money $money): float
    {
        $commissionFee = (($money->getAmount() * getenv('MONEY_DEPOSIT_PERCENT')) / 100);

        if ($commissionFee >= getenv('MONEY_DEPOSIT_MAXIMUM_COMMISSION')) {
            return floatval(getenv('MONEY_DEPOSIT_MAXIMUM_COMMISSION'));
        }

        return $commissionFee;
    }

    protected function cashOutForLegalPeople(Money $money): float
    {
        $commissionFee = ($money->getAmount() * getenv('CASH_CLEARING_PERCENT')) / 100;

        if ($commissionFee <= getenv('CASH_CLEARING_MINIMUM_COMMISSION')) {
            return floatval(getenv('CASH_CLEARING_MINIMUM_COMMISSION'));
        }

        return $commissionFee;
    }

    protected function cashOutForNaturalPeople(
        Money $money,
        Discount $discountInformation
    ): float {

        if ($discountInformation->getOperationNumber() > getenv('CASH_CLEARING_AMOUNT_OF_TIMES_FREE')) {
            $commissionFee = ($money->getAmount() * getenv('CASH_CLEARING_PERCENT')) / 100;
        } else {
            if ($discountInformation->getDiscount()->getAmount() === 0) {
                $commissionFee = ($money->getAmount() * getenv('CASH_CLEARING_PERCENT')) / 100;
            } elseif ($discountInformation->getDiscount()->getAmount() > 0 &&
                $discountInformation->getDiscount()->getAmount() > $money->getAmount()) {
                $commissionFee = 0;
            } else {
                $commissionFee = ((
                    $money->getAmount() - $discountInformation->getDiscount()->getAmount()
                        ) * getenv('CASH_CLEARING_PERCENT')) / 100;
            }
        }

        return $commissionFee;
    }
}
