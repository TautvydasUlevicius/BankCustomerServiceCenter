<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;
use App\Util\Validator;
use Evp\Component\Money\Money;

class OperationManager
{
    private $validator;
    private $fileManager;

    public function __construct(
        Validator $validator,
        FileManager $fileManager
    ) {
        $this->validator = $validator;
        $this->fileManager = $fileManager;
    }

    public function createOperationsFromFile(string $fileLocation, string $dataType)
    {
        $this->validator->checkIfFileExists($fileLocation);
        $this->validator->checkIfFileTypeIsSupported($fileLocation);
        $this->validator->checkIfDataTypeIsSupported($dataType);
        $this->validator->compareFileTypeAndDataType($fileLocation, $dataType);
        $operations = $this->fileManager->{'getOperationsFrom' . lcfirst($dataType)}($fileLocation);

        return $this->createArrayOfOperationObjects($operations);
    }

    protected function createArrayOfOperationObjects(array $operations): array
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
                ->setMoney(new Money($operation[4], $operation[5]))
            ;
            $counter++;
        }

        return $arrayOfOperationObjects;
    }
}
