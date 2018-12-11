<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;

class CommissionManager
{
    public function calculateCommission(
        array $arrayOfOperationObjects,
        array $discountInformation
    ) {
        $arrayOfCalculatedCommissions = [];

        for ($i = 0; $i < count($arrayOfOperationObjects); $i++) {
            if ($arrayOfOperationObjects[$i]->getOperationType() === 'cash_in') {
                $arrayOfCalculatedCommissions[$i] = $this->moneyDeposit($arrayOfOperationObjects[$i]->getAmount());
            } elseif ($arrayOfOperationObjects[$i]->getOperationType() === 'cash_out' &&
                $arrayOfOperationObjects[$i]->getUserType() === 'legal') {
                $arrayOfCalculatedCommissions[$i] = $this->cashClearingForLegalPeople(
                    $arrayOfOperationObjects[$i]->getAmount()
                );
            } else {
                $arrayOfCalculatedCommissions[$i] = $this->cashClearingForNaturalPeople(
                    $arrayOfOperationObjects[$i]->getAmount(),
                    $discountInformation[$i]
                );
            }
        }

        return $arrayOfCalculatedCommissions;
    }

    protected function moneyDeposit(float $amount): float
    {
        $commissionFee = (($amount * getenv('MONEY_DEPOSIT_PERCENT')) / 100);

        if ($commissionFee >= getenv('MONEY_DEPOSIT_MAXIMUM_COMMISSION')) {
            return floatval(getenv('MONEY_DEPOSIT_MAXIMUM_COMMISSION'));
        }

        return $commissionFee;
    }

    protected function cashClearingForLegalPeople(float $amount): float
    {
        $commissionFee = ($amount * getenv('CASH_CLEARING_PERCENT')) / 100;

        if ($commissionFee <= getenv('CASH_CLEARING_MINIMUM_COMMISSION')) {
            return floatval(getenv('CASH_CLEARING_MINIMUM_COMMISSION'));
        }

        return $commissionFee;
    }

    protected function cashClearingForNaturalPeople(
        float $amount,
        Discount $discountInformation
    ): float {

        if ($discountInformation->getOperationNumber() > getenv('CASH_CLEARING_AMOUNT_OF_TIMES_FREE')) {
            $commissionFee = ($amount * getenv('CASH_CLEARING_PERCENT')) / 100;
        } else {
            if ($discountInformation->getDiscountAmountLeft() === 0) {
                $commissionFee = ($amount * getenv('CASH_CLEARING_PERCENT')) / 100;
            } elseif ($discountInformation->getDiscountAmountLeft() > 0 &&
                $discountInformation->getDiscountAmountLeft() > $amount) {
                $commissionFee = 0;
            } else {
                $commissionFee = (
                    ($amount - $discountInformation->getDiscountAmountLeft()) * getenv('CASH_CLEARING_PERCENT')
                    ) / 100;
            }
        }

        return $commissionFee;
    }
}
