<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use Evp\Component\Money\Money;
use Exception;

class CommissionManager
{
    private $legalPeople;
    private $moneyDeposit;
    private $mainCurrency;
    private $discountManager;
    private $moneyDepositPercent;
    private $cashClearingPercent;
    private $moneyDepositMaximumCommission;
    private $cashClearingMinimumCommission;
    private $cashClearingAmountOfTimesFree;

    public function __construct(
        string $legalPeople,
        string $moneyDeposit,
        string $mainCurrency,
        string $moneyDepositPercent,
        string $cashClearingPercent,
        string $moneyDepositMaximumCommission,
        string $cashClearingMinimumCommission,
        string $cashClearingAmountOfTimesFree,
        DiscountManager $discountManager
    ) {
        $this->legalPeople = $legalPeople;
        $this->moneyDeposit = $moneyDeposit;
        $this->mainCurrency = $mainCurrency;
        $this->discountManager = $discountManager;
        $this->moneyDepositPercent = $moneyDepositPercent;
        $this->cashClearingPercent = $cashClearingPercent;
        $this->moneyDepositMaximumCommission = $moneyDepositMaximumCommission;
        $this->cashClearingMinimumCommission = $cashClearingMinimumCommission;
        $this->cashClearingAmountOfTimesFree = $cashClearingAmountOfTimesFree;
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
            if ($operation->getOperationType() === $this->moneyDeposit) {
                $commission = $this->moneyDeposit($operation->getMoney());
            } elseif ($operation->getUserType() === $this->legalPeople) {
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
                    ->setCurrency($this->mainCurrency)
            ;

            $counter++;
        }

        return $calculatedCommissions;
    }

    protected function moneyDeposit(Money $money): Money
    {
        $commission = $money->mul($this->moneyDepositPercent)->div(100);

        if ($commission->getAmount() >= $this->moneyDepositMaximumCommission) {
            return (new Money())->setAmount($this->moneyDepositMaximumCommission);
        }

        return $commission;
    }

    protected function moneyWithdrawalForLegalPeople(Money $money): Money
    {
        $commission = $money->mul($this->cashClearingPercent)->div(100);

        if ($commission->getAmount() <= $this->cashClearingMinimumCommission) {
            return (new Money())->setAmount($this->cashClearingMinimumCommission);
        }

        return $commission;
    }

    protected function moneyWithdrawalForNaturalPeople(
        Money $money,
        Discount $discountInformation
    ): Money {
        if ($discountInformation->getOperationNumber() > $this->cashClearingAmountOfTimesFree) {
            $commissionFee = $money->mul($this->cashClearingPercent)->div(100);
        } else {
            if ($discountInformation->getMoney()->getAmount() === 0) {
                $commissionFee = $money->mul($this->cashClearingPercent)->div(100);
            } elseif ($discountInformation->getMoney()->getAmount() > 0 &&
                $discountInformation->getMoney()->getAmount() > $money->getAmount()) {
                $commissionFee = $money->setAmount(0);
            } else {
                $originalCurrency = $money->getCurrency();

                $commissionFee = $money
                    ->setCurrency($this->mainCurrency)
                    ->sub($discountInformation->getMoney())
                    ->mul($this->cashClearingPercent)
                    ->div(100)
                ;

                $money->setCurrency($originalCurrency);
            }
        }

        return $commissionFee;
    }
}
