<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use Evp\Component\Money\Money;
use Exception;

class CommissionManager
{
    private $configuration;
    private $discountManager;

    public function __construct(
        array $configuration,
        DiscountManager $discountManager
    ) {
        $this->configuration = $configuration;
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
            if ($operation->getOperationType() === $this->configuration['money_deposit']) {
                $commission = $this->moneyDeposit($operation->getMoney());
            } elseif ($operation->getUserType() === $this->configuration['legal_people']) {
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
                    ->setCurrency($this->configuration['main_currency'])
            ;

            $counter++;
        }

        return $calculatedCommissions;
    }

    protected function moneyDeposit(Money $money): Money
    {
        $commission = $money->mul($this->configuration['deposit_percent'])->div(100);

        if ($commission->getAmount() >= $this->configuration['deposit_maximum_commission']) {
            return (new Money())->setAmount($this->configuration['deposit_maximum_commission']);
        }

        return $commission;
    }

    protected function moneyWithdrawalForLegalPeople(Money $money): Money
    {
        $commission = $money->mul($this->configuration['withdrawal_percent'])->div(100);

        if ($commission->getAmount() <= $this->configuration['withdrawal_minimum_commission']) {
            return (new Money())->setAmount($this->configuration['withdrawal_minimum_commission']);
        }

        return $commission;
    }

    protected function moneyWithdrawalForNaturalPeople(
        Money $money,
        Discount $discountInformation
    ): Money {
        if ($discountInformation->getOperationNumber() > $this->configuration['withdrawal_amount_of_times_free']) {
            $commissionFee = $money->mul($this->configuration['withdrawal_percent'])->div(100);
        } else {
            if ($discountInformation->getMoney()->getAmount() === 0) {
                $commissionFee = $money->mul($this->configuration['withdrawal_percent'])->div(100);
            } elseif ($discountInformation->getMoney()->getAmount() > 0 &&
                $discountInformation->getMoney()->getAmount() > $money->getAmount()) {
                $commissionFee = $money->setAmount(0);
            } else {
                $originalCurrency = $money->getCurrency();

                $commissionFee = $money
                    ->setCurrency($this->configuration['main_currency'])
                    ->sub($discountInformation->getMoney())
                    ->mul($this->configuration['withdrawal_percent'])
                    ->div(100)
                ;

                $money->setCurrency($originalCurrency);
            }
        }

        return $commissionFee;
    }
}
