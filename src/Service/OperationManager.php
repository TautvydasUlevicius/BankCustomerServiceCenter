<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;

class OperationManager
{
    public function createArrayOfOperationObjects(array $operations): array
    {
        $counter = 0;

        $arrayOfOperationObjects = [];

        foreach ($operations as $operation) {
            $arrayOfOperationObjects[$counter] = (new Operation())
                ->setOperationId($counter)
                ->setDate($operation[0])
                ->setUserId($operation[1])
                ->setUserType($operation[2])
                ->setOperationType($operation[3])
                ->setMoney($operation[4], $operation[5])
            ;
            $counter++;
        }

        return $arrayOfOperationObjects;
    }
}
