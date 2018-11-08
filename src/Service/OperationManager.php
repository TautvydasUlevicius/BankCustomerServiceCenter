<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;

class OperationManager
{
    public function createOperationObject(array $operation): Operation
    {
        $operationObject = new Operation();
        $operationObject->setDate($operation[0]);
        $operationObject->setUserId($operation[1]);
        $operationObject->setUserType($operation[2]);
        $operationObject->setOperationType($operation[3]);
        $operationObject->setAmount(floatval($operation[4]));
        $operationObject->setCurrency($operation[5]);

        return $operationObject;
    }
}
