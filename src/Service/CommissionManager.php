<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use Evp\Component\Money\Money;
use Exception;

class CommissionManager
{
    private $discountManager;

    public function __construct(DiscountManager $discountManager)
    {
        $this->discountManager = $discountManager;
    }

    public function calculateCommission(array $arrayOfOperationObjects): array
    {
        $discountInformation = $this->discountManager->calculateDiscountForOperations($arrayOfOperationObjects);

        if (empty($discountInformation)) {
            throw new Exception('Missing discount information');
        }

        $counter = 0;

        $calculatedCommissions = [];
        foreach ($arrayOfOperationObjects as $operation) {
            if ($operation->getOperationType() === $_ENV['MONEY_DEPOSIT']) {
                $commission = $this->moneyDeposit($operation->getMoney());
            } elseif ($operation->getUserType() === $_ENV['LEGAL_PEOPLE']) {
                $commission = $this->moneyWithdrawalForLegalPeople($operation->getMoney());
            } else {
                $commission = $this->moneyWithdrawalForNaturalPeople(
                    $operation->getMoney(),
                    $discountInformation[$counter]
                );
            }

            $calculatedCommissions[$counter] =
                (new Money())
                    ->setAmount($commission->getAmount())
                    ->setCurrency($_ENV['MAIN_CURRENCY'])
            ;

            $counter++;
        }

        return $calculatedCommissions;
    }

    protected function moneyDeposit(Money $money): Money
    {
        $commission = $money->mul($_ENV['MONEY_DEPOSIT_PERCENT'])->div(100);

        if ($commission->getAmount() >= $_ENV['MONEY_DEPOSIT_MAXIMUM_COMMISSION']) {
            return (new Money())->setAmount($_ENV['MONEY_DEPOSIT_MAXIMUM_COMMISSION']);
        }

        return $commission;
    }

    protected function moneyWithdrawalForLegalPeople(Money $money): Money
    {
        $commission = $money->mul($_ENV['CASH_CLEARING_PERCENT'])->div(100);

        if ($commission->getAmount() <= $_ENV['CASH_CLEARING_MINIMUM_COMMISSION']) {
            return (new Money())->setAmount($_ENV['CASH_CLEARING_MINIMUM_COMMISSION']);
        }

        return $commission;
    }

    protected function moneyWithdrawalForNaturalPeople(
        Money $money,
        Discount $discountInformation
    ): Money {
        if ($discountInformation->getOperationNumber() > $_ENV['CASH_CLEARING_AMOUNT_OF_TIMES_FREE']) {
            $commissionFee = $money->mul($_ENV['CASH_CLEARING_PERCENT'])->div(100);
        } else {
            if ($discountInformation->getMoney()->getAmount() === 0) {
                $commissionFee = $money->mul($_ENV['CASH_CLEARING_PERCENT'])->div(100);
            } elseif ($discountInformation->getMoney()->getAmount() > 0 &&
                $discountInformation->getMoney()->getAmount() > $money->getAmount()) {
                $commissionFee = $money->setAmount(0);
            } else {
                $originalCurrency = $money->getCurrency();

                $commissionFee = $money
                    ->setCurrency($_ENV['MAIN_CURRENCY'])
                    ->sub($discountInformation->getMoney())
                    ->mul($_ENV['CASH_CLEARING_PERCENT'])
                    ->div(100)
                ;

                $money->setCurrency($originalCurrency);
            }
        }

        return $commissionFee;
    }
}
