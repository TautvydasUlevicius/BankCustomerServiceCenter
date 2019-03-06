<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use Evp\Component\Money\Money;
use Exception;

class CommissionManager
{
    private $legalPeople;
    private $mainCurrency;
    private $moneyDeposit;
    private $depositPercent;
    private $discountManager;
    private $withdrawalPercent;
    private $depositMaximumCommission;
    private $withdrawalMinimumCommission;
    private $withdrawalAmountOfTimesFree;

    public function __construct(
        string $legalPeople,
        string $moneyDeposit,
        string $mainCurrency,
        string $depositPercent,
        string $withdrawalPercent,
        DiscountManager $discountManager,
        string $depositMaximumCommission,
        string $withdrawalMinimumCommission,
        string $withdrawalAmountOfTimesFree
    ) {
        $this->legalPeople = $legalPeople;
        $this->moneyDeposit = $moneyDeposit;
        $this->mainCurrency = $mainCurrency;
        $this->depositPercent = $depositPercent;
        $this->discountManager = $discountManager;
        $this->withdrawalPercent = $withdrawalPercent;
        $this->depositMaximumCommission = $depositMaximumCommission;
        $this->withdrawalMinimumCommission = $withdrawalMinimumCommission;
        $this->withdrawalAmountOfTimesFree = $withdrawalAmountOfTimesFree;
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
        $commission = $money->mul($this->depositPercent)->div(100);

        if ($commission->getAmount() >= $this->depositMaximumCommission) {
            return (new Money())->setAmount($this->depositMaximumCommission);
        }

        return $commission;
    }

    protected function moneyWithdrawalForLegalPeople(Money $money): Money
    {
        $commission = $money->mul($this->withdrawalPercent)->div(100);

        if ($commission->getAmount() <= $this->withdrawalMinimumCommission) {
            return (new Money())->setAmount($this->withdrawalMinimumCommission);
        }

        return $commission;
    }

    protected function moneyWithdrawalForNaturalPeople(
        Money $money,
        Discount $discountInformation
    ): Money {
        if ($discountInformation->getOperationNumber() > $this->withdrawalAmountOfTimesFree) {
            $commissionFee = $money->mul($this->withdrawalPercent)->div(100);
        } else {
            if ($discountInformation->getMoney()->getAmount() === 0) {
                $commissionFee = $money->mul($this->withdrawalPercent)->div(100);
            } elseif ($discountInformation->getMoney()->getAmount() > 0 &&
                $discountInformation->getMoney()->getAmount() > $money->getAmount()) {
                $commissionFee = $money->setAmount(0);
            } else {
                $originalCurrency = $money->getCurrency();

                $commissionFee = $money
                    ->setCurrency($this->mainCurrency)
                    ->sub($discountInformation->getMoney())
                    ->mul($this->withdrawalPercent)
                    ->div(100)
                ;

                $money->setCurrency($originalCurrency);
            }
        }

        return $commissionFee;
    }
}
