<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Discount;
use App\Util\DateChecker;

class DiscountManager
{
    private $dateChecker;

    public function __construct(DateChecker $dateChecker)
    {
        $this->dateChecker = $dateChecker;
    }

    public function calculateDiscountForOperations(array $operationObjects): array
    {
        $discountInformation = [];

        for ($i = 0; $i < count($operationObjects); $i++) {
            $counter = 0;
            $operationNumber = 1;
            $discountAmount = floatval(getenv('CASH_CLEARING_FREE_AMOUNT'));

            while ($counter < $i) {
                if ($operationObjects[$i]->getUserId() === $operationObjects[$counter]->getUserId() &&
                    $operationObjects[$i]->getUserType() === $operationObjects[$counter]->getUserType() &&
                    $operationObjects[$i]->getOperationType() === $operationObjects[$counter]->getOperationType() &&
                    $this->dateChecker->checkIfTwoDatesOnSameWeek($operationObjects[$i]->getDate(), $operationObjects[$counter]->getDate()) === true
                ) {
                    $operationNumber++;
                    $discountAmount -= $operationObjects[$counter]->getMoney()->getAmount();

                    if ($discountAmount < 0) {
                        $discountAmount = 0;
                    }
                }
                $counter++;
            }

            $discountInformation[$i] = (new Discount())
                ->setOperationId($operationObjects[$i]->getOperationId())
                ->setOperationNumber($operationNumber)
                ->setDiscount($discountAmount, getenv('MAIN_CURRENCY'))
            ;
        }

        return $discountInformation;
    }
}
