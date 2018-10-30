<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;

class OperationManager
{
    private $operation;

    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
    }

    public function createOperationObject(array $operation): Operation
    {
        $this->operation->setDate($operation[0]);
        $this->operation->setUserId($operation[1]);
        $this->operation->setUserType($operation[2]);
        $this->operation->setOperationType($operation[3]);
        $this->operation->setAmount(floatval($operation[4]));
        $this->operation->setCurrency($operation[5]);

        return $this->operation;
    }
}
