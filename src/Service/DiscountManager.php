<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use App\Util\DateChecker;
use Evp\Component\Money\Money;

class DiscountManager
{
    private $dateChecker;
    private $mainCurrency;
    private $cashClearingFreeAmount;

    public function __construct(
        string $mainCurrency,
        string $cashClearingFreeAmount,
        DateChecker $dateChecker
    ) {
        $this->dateChecker = $dateChecker;
        $this->mainCurrency = $mainCurrency;
        $this->cashClearingFreeAmount = $cashClearingFreeAmount;
    }

    public function calculateDiscountForOperations(array $operationObjects): array
    {
        $discountInformation = [];

        for ($i = 0; $i < count($operationObjects); $i++) {
            $counter = 0;
            $operationNumber = 1;
            $discountAmount = new Money($this->cashClearingFreeAmount, $this->mainCurrency);

            while ($counter < $i) {
                if ($operationObjects[$i]->getUserId() === $operationObjects[$counter]->getUserId() &&
                    $operationObjects[$i]->getUserType() === $operationObjects[$counter]->getUserType() &&
                    $operationObjects[$i]->getOperationType() === $operationObjects[$counter]->getOperationType() &&
                    $this->dateChecker->checkIfTwoDatesOnSameWeek($operationObjects[$i]->getDate(), $operationObjects[$counter]->getDate()) === true
                ) {
                    $operationNumber++;

                    $originalCurrency = $operationObjects[$counter]->getMoney()->getCurrency();
                    $discountAmountLeft = $discountAmount->sub(
                        $operationObjects[$counter]->getMoney()->setCurrency($this->mainCurrency)
                    );
                    $discountAmount->setAmount($discountAmountLeft->getAmount());
                    $operationObjects[$counter]->getMoney()->setCurrency($originalCurrency);

                    if ($discountAmount->getAmount() < 0) {
                        $discountAmount->setAmount(0);
                    }
                }
                $counter++;
            }

            $discountInformation[$i] = (new Discount())
                ->setOperationId($operationObjects[$i]->getOperationId())
                ->setOperationNumber($operationNumber)
                ->setMoney($discountAmount)
            ;
        }

        return $discountInformation;
    }
}
