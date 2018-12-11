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

    public function calculateDiscountForOperations(array $arrayOfOperationObjects): array
    {
        $discountInformation = [];

        for ($i = 0; $i < count($arrayOfOperationObjects); $i++) {
            $discountAmount = floatval(getenv('CASH_CLEARING_FREE_AMOUNT'));
            $counter = 0;
            $operationNumber = 1;

            while ($counter < $i) {
                if ($arrayOfOperationObjects[$i]->getUserId() === $arrayOfOperationObjects[$counter]->getUserId() &&
                    $arrayOfOperationObjects[$i]->getUserType() === $arrayOfOperationObjects[$counter]->getUserType() &&
                    $arrayOfOperationObjects[$i]->getOperationType() === $arrayOfOperationObjects[$counter]->getOperationType() &&
                    $this->dateChecker->checkIfTwoDatesOnSameWeek(
                        $arrayOfOperationObjects[$i]->getDate(),
                        $arrayOfOperationObjects[$counter]->getDate()
                    ) === true
                ) {
                    $operationNumber++;
                    $discountAmount -= $arrayOfOperationObjects[$counter]->getAmount();
                    if ($discountAmount < 0) {
                        $discountAmount = 0;
                    }
                }
                $counter++;
            }

            $discountInformation[$i] = (new Discount())
            ->setOperationId($arrayOfOperationObjects[$i]->getOperationId())
            ->setOperationNumber($operationNumber)
            ->setDiscountAmountLeft($discountAmount)
            ;
        }
        return $discountInformation;
    }
}
